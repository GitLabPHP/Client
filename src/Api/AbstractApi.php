<?php

declare(strict_types=1);

namespace Gitlab\Api;

use Gitlab\Client;
use Gitlab\Exception\RuntimeException;
use Gitlab\HttpClient\Message\ResponseMediator;
use Gitlab\HttpClient\Util\JsonArray;
use Gitlab\HttpClient\Util\QueryStringBuilder;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Message\MultipartStream\MultipartStreamBuilder;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * The HTTP methods client.
     *
     * @var HttpMethodsClientInterface
     */
    private $httpClient;

    /**
     * The HTTP stream factory.
     *
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * @param Client $client
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->httpClient = $client->getHttpClient();
        $this->streamFactory = $client->getStreamFactory();
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
    protected function getAsResponse($uri, array $params = [], array $headers = [])
    {
        return $this->httpClient->get(self::prepareUri($uri, $params), $headers);
    }

    /**
     * @param string               $uri
     * @param array<string,mixed>  $params
     * @param array<string,string> $headers
     *
     * @return mixed
     */
    protected function get($uri, array $params = [], array $headers = [])
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
    protected function post($uri, array $params = [], array $headers = [], array $files = [])
    {
        if (0 < count($files)) {
            $builder = $this->createMultipartStreamBuilder($params, $files);
            $body = self::prepareMultipartBody($builder);
            $headers = self::addMultipartContentType($headers, $builder);
        } else {
            $body = self::prepareJsonBody($params);

            if (null !== $body) {
                $headers = self::addJsonContentType($headers);
            }
        }

        $response = $this->httpClient->post(self::prepareUri($uri), $headers, $body);

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
    protected function put($uri, array $params = [], array $headers = [], array $files = [])
    {
        if (0 < count($files)) {
            $builder = $this->createMultipartStreamBuilder($params, $files);
            $body = self::prepareMultipartBody($builder);
            $headers = self::addMultipartContentType($headers, $builder);
        } else {
            $body = self::prepareJsonBody($params);

            if (null !== $body) {
                $headers = self::addJsonContentType($headers);
            }
        }

        $response = $this->httpClient->put(self::prepareUri($uri), $headers, $body ?? '');

        return ResponseMediator::getContent($response);
    }

    /**
     * @param string               $uri
     * @param array<string,mixed>  $params
     * @param array<string,string> $headers
     *
     * @return mixed
     */
    protected function delete($uri, array $params = [], array $headers = [])
    {
        $body = self::prepareJsonBody($params);

        if (null !== $body) {
            $headers = self::addJsonContentType($headers);
        }

        $response = $this->httpClient->delete(self::prepareUri($uri), $headers, $body ?? '');

        return ResponseMediator::getContent($response);
    }

    /**
     * @param int|string $uri
     *
     * @return string
     */
    protected static function encodePath($uri)
    {
        return rawurlencode((string) $uri);
    }

    /**
     * @param int|string $id
     * @param string     $uri
     *
     * @return string
     */
    protected function getProjectPath($id, $uri)
    {
        return 'projects/'.self::encodePath($id).'/'.$uri;
    }

    /**
     * @param int    $id
     * @param string $uri
     *
     * @return string
     */
    protected function getGroupPath($id, $uri)
    {
        return 'groups/'.self::encodePath($id).'/'.$uri;
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
        $query = array_filter($query, function ($value): bool {
            return null !== $value;
        });

        return sprintf('%s%s%s', self::URI_PREFIX, $uri, QueryStringBuilder::build($query));
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
        $builder = new MultipartStreamBuilder($this->streamFactory);

        foreach ($params as $name => $value) {
            $builder->addResource($name, $value);
        }

        foreach ($files as $name => $file) {
            $builder->addResource($name, self::tryFopen($file, 'r'), [
                'headers' => [
                    'Content-Type' => self::guessFileContentType($file),
                ],
                'filename' => basename($file),
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
        $contentType = sprintf('%s; boundary=%s', ResponseMediator::MULTIPART_CONTENT_TYPE, $builder->getBoundary());

        return array_merge(['Content-Type' => $contentType], $headers);
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
        $params = array_filter($params, function ($value): bool {
            return null !== $value;
        });

        if (0 === count($params)) {
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
        return array_merge(['Content-Type' => ResponseMediator::JSON_CONTENT_TYPE], $headers);
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
    private static function tryFopen($filename, $mode)
    {
        $ex = null;
        set_error_handler(function () use ($filename, $mode, &$ex) {
            $ex = new RuntimeException(sprintf(
                'Unable to open %s using mode %s: %s',
                $filename,
                $mode,
                func_get_args()[1]
            ));
        });

        $handle = fopen($filename, $mode);
        restore_error_handler();

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
        if (!class_exists(\finfo::class, false)) {
            return ResponseMediator::STREAM_CONTENT_TYPE;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $type = $finfo->file($file);

        return false !== $type ? $type : ResponseMediator::STREAM_CONTENT_TYPE;
    }
}
