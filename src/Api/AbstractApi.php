<?php

declare(strict_types=1);

namespace Gitlab\Api;

use Gitlab\Client;
use Gitlab\Exception\RuntimeException;
use Gitlab\HttpClient\Message\ResponseMediator;
use Gitlab\HttpClient\Util\JsonArray;
use Gitlab\HttpClient\Util\QueryStringBuilder;
use Http\Message\MultipartStream\MultipartStreamBuilder;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use ValueError;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Matt Humphrey <matt@m4tt.co>
 * @author Radu Topala <radu.topala@trisoft.ro>
 */
abstract class AbstractApi implements ApiInterface
{
    /**
     * The URI prefix.
     *
     * @var string
     */
    private const URI_PREFIX = '/api/v4/';

    /**
     * The client instance.
     *
     * @var Client
     */
    private $client;

    /**
     * The per page parameter.
     *
     * @var int|null
     */
    private $perPage;

    /**
     * Create a new API instance.
     *
     * @param Client   $client
     * @param int|null $perPage
     *
     * @return void
     */
    public function __construct(Client $client, int $perPage = null)
    {
        if (null !== $perPage && ($perPage < 1 || $perPage > 100)) {
            throw new ValueError(\sprintf('%s::__construct(): Argument #2 ($perPage) must be between 1 and 100, or null', self::class));
        }

        $this->client = $client;
        $this->perPage = $perPage;
    }

    /**
     * Create a new instance with the given page parameter.
     *
     * This must be an integer between 1 and 100.
     *
     * @param int|null $perPage
     *
     * @return static
     */
    public function perPage(?int $perPage)
    {
        if (null !== $perPage && ($perPage < 1 || $perPage > 100)) {
            throw new ValueError(\sprintf('%s::perPage(): Argument #1 ($perPage) must be between 1 and 100, or null', self::class));
        }

        $copy = clone $this;

        $copy->perPage = $perPage;

        return $copy;
    }

    /**
     * Send a GET request with query params and return the raw response.
     *
     * @param string               $uri
     * @param array                $params
     * @param array<string,string> $headers
     *
     * @throws \Http\Client\Exception
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function getAsResponse(string $uri, array $params = [], array $headers = [])
    {
        if (null !== $this->perPage && !isset($params['per_page'])) {
            $params['per_page'] = $this->perPage;
        }

        return $this->client->getHttpClient()->get(self::prepareUri($uri, $params), $headers);
    }

    /**
     * @param string               $uri
     * @param array<string,mixed>  $params
     * @param array<string,string> $headers
     *
     * @return mixed
     */
    protected function get(string $uri, array $params = [], array $headers = [])
    {
        $response = $this->getAsResponse($uri, $params, $headers);

        return ResponseMediator::getContent($response);
    }

    /**
     * @param string               $uri
     * @param array<string,mixed>  $params
     * @param array<string,string> $headers
     * @param array<string,string> $files
     *
     * @return mixed
     */
    protected function post(string $uri, array $params = [], array $headers = [], array $files = [])
    {
        if (0 < \count($files)) {
            $builder = $this->createMultipartStreamBuilder($params, $files);
            $body = self::prepareMultipartBody($builder);
            $headers = self::addMultipartContentType($headers, $builder);
        } else {
            $body = self::prepareJsonBody($params);

            if (null !== $body) {
                $headers = self::addJsonContentType($headers);
            }
        }

        $response = $this->client->getHttpClient()->post(self::prepareUri($uri), $headers, $body);

        return ResponseMediator::getContent($response);
    }

    /**
     * @param string               $uri
     * @param array<string,mixed>  $params
     * @param array<string,string> $headers
     * @param array<string,string> $files
     *
     * @return mixed
     */
    protected function put(string $uri, array $params = [], array $headers = [], array $files = [])
    {
        if (0 < \count($files)) {
            $builder = $this->createMultipartStreamBuilder($params, $files);
            $body = self::prepareMultipartBody($builder);
            $headers = self::addMultipartContentType($headers, $builder);
        } else {
            $body = self::prepareJsonBody($params);

            if (null !== $body) {
                $headers = self::addJsonContentType($headers);
            }
        }

        $response = $this->client->getHttpClient()->put(self::prepareUri($uri), $headers, $body ?? '');

        return ResponseMediator::getContent($response);
    }

    /**
     * @param string               $uri
     * @param array<string,mixed>  $params
     * @param array<string,string> $headers
     *
     * @return mixed
     */
    protected function delete(string $uri, array $params = [], array $headers = [])
    {
        $body = self::prepareJsonBody($params);

        if (null !== $body) {
            $headers = self::addJsonContentType($headers);
        }

        $response = $this->client->getHttpClient()->delete(self::prepareUri($uri), $headers, $body ?? '');

        return ResponseMediator::getContent($response);
    }

    /**
     * @param int|string $uri
     *
     * @return string
     */
    protected static function encodePath($uri)
    {
        return \rawurlencode((string) $uri);
    }

    /**
     * @param int|string $id
     * @param string     $uri
     *
     * @return string
     */
    protected function getProjectPath($id, string $uri)
    {
        return 'projects/'.self::encodePath($id).'/'.$uri;
    }

    /**
     * Create a new OptionsResolver with page and per_page options.
     *
     * @return OptionsResolver
     */
    protected function createOptionsResolver()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('page')
            ->setAllowedTypes('page', 'int')
            ->setAllowedValues('page', function ($value) {
                return $value > 0;
            })
        ;
        $resolver->setDefined('per_page')
            ->setAllowedTypes('per_page', 'int')
            ->setAllowedValues('per_page', function ($value) {
                return $value > 0 && $value <= 100;
            })
        ;

        return $resolver;
    }

    /**
     * Prepare the request URI.
     *
     * @param string $uri
     * @param array  $query
     *
     * @return string
     */
    private static function prepareUri(string $uri, array $query = [])
    {
        $query = \array_filter($query, function ($value): bool {
            return null !== $value;
        });

        return \sprintf('%s%s%s', self::URI_PREFIX, $uri, QueryStringBuilder::build($query));
    }

    /**
     * Prepare the request URI.
     *
     * @param array<string,mixed>  $params
     * @param array<string,string> $files
     *
     * @return MultipartStreamBuilder
     */
    private function createMultipartStreamBuilder(array $params = [], array $files = [])
    {
        $builder = new MultipartStreamBuilder($this->client->getStreamFactory());

        foreach ($params as $name => $value) {
            $builder->addResource($name, $value);
        }

        foreach ($files as $name => $file) {
            $builder->addResource($name, self::tryFopen($file, 'r'), [
                'headers' => [
                    'Content-Type' => self::guessFileContentType($file),
                ],
                'filename' => \basename($file),
            ]);
        }

        return $builder;
    }

    /**
     * Prepare the request multipart body.
     *
     * @param MultipartStreamBuilder $builder
     *
     * @return StreamInterface
     */
    private static function prepareMultipartBody(MultipartStreamBuilder $builder)
    {
        return $builder->build();
    }

    /**
     * Add the multipart content type to the headers if one is not already present.
     *
     * @param array<string,string>   $headers
     * @param MultipartStreamBuilder $builder
     *
     * @return array<string,string>
     */
    private static function addMultipartContentType(array $headers, MultipartStreamBuilder $builder)
    {
        $contentType = \sprintf('%s; boundary=%s', ResponseMediator::MULTIPART_CONTENT_TYPE, $builder->getBoundary());

        return \array_merge(['Content-Type' => $contentType], $headers);
    }

    /**
     * Prepare the request JSON body.
     *
     * @param array<string,mixed> $params
     *
     * @return string|null
     */
    private static function prepareJsonBody(array $params)
    {
        $params = \array_filter($params, function ($value): bool {
            return null !== $value;
        });

        if (0 === \count($params)) {
            return null;
        }

        return JsonArray::encode($params);
    }

    /**
     * Add the JSON content type to the headers if one is not already present.
     *
     * @param array<string,string> $headers
     *
     * @return array<string,string>
     */
    private static function addJsonContentType(array $headers)
    {
        return \array_merge(['Content-Type' => ResponseMediator::JSON_CONTENT_TYPE], $headers);
    }

    /**
     * Safely opens a PHP stream resource using a filename.
     *
     * When fopen fails, PHP normally raises a warning. This function adds an
     * error handler that checks for errors and throws an exception instead.
     *
     * @param string $filename File to open
     * @param string $mode     Mode used to open the file
     *
     * @return resource
     *
     * @throws RuntimeException if the file cannot be opened
     *
     * @see https://github.com/guzzle/psr7/blob/1.6.1/src/functions.php#L287-L320
     */
    private static function tryFopen(string $filename, string $mode)
    {
        $ex = null;
        \set_error_handler(function () use ($filename, $mode, &$ex) {
            $ex = new RuntimeException(\sprintf(
                'Unable to open %s using mode %s: %s',
                $filename,
                $mode,
                \func_get_args()[1]
            ));
        });

        $handle = \fopen($filename, $mode);
        \restore_error_handler();

        if (null !== $ex) {
            throw $ex;
        }

        /** @var resource */
        return $handle;
    }

    /**
     * Guess the content type of the file if possible.
     *
     * @param string $file
     *
     * @return string
     */
    private static function guessFileContentType(string $file)
    {
        if (!\class_exists(\finfo::class, false)) {
            return ResponseMediator::STREAM_CONTENT_TYPE;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $type = $finfo->file($file);

        return false !== $type ? $type : ResponseMediator::STREAM_CONTENT_TYPE;
    }
}
