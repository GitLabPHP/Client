<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Api;

use Gitlab\Client;
use Gitlab\Exception\RuntimeException;
use Gitlab\HttpClient\Message\ResponseMediator;
use Gitlab\HttpClient\Util\JsonArray;
use Gitlab\HttpClient\Util\QueryStringBuilder;
use Http\Message\MultipartStream\MultipartStreamBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Matt Humphrey <matt@m4tt.co>
 * @author Radu Topala <radu.topala@trisoft.ro>
 */
abstract class AbstractApi
{
    /**
     * The URI prefix.
     *
     * @var string
     */
    private const URI_PREFIX = '/api/v4/';

    /**
     * The access levels for groups and projects
     * as defined in the Gitlab::Access module.
     *
     * @see https://gitlab.com/gitlab-org/gitlab/-/blob/master/lib/gitlab/access.rb
     *
     * @var array
     */
    protected const ACCESS_LEVELS = [0, 10, 20, 30, 40, 50];

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
     * @param Client $client
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
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
    protected function getAsResponse(string $uri, array $params = [], array $headers = []): ResponseInterface
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
     * @param array<string,mixed>  $uriParams
     *
     * @return mixed
     */
    protected function post(string $uri, array $params = [], array $headers = [], array $files = [], array $uriParams = [])
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

        $response = $this->client->getHttpClient()->post(self::prepareUri($uri, $uriParams), $headers, $body);

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
     * @param array<string,string> $files
     *
     * @return mixed
     */
    protected function patch(string $uri, array $params = [], array $headers = [], array $files = [])
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

        $response = $this->client->getHttpClient()->patch(self::prepareUri($uri), $headers, $body ?? '');

        return ResponseMediator::getContent($response);
    }

    /**
     * @param string               $uri
     * @param string               $file
     * @param array<string,string> $headers
     * @param array<string,mixed>  $uriParams
     *
     * @return mixed
     */
    protected function putFile(string $uri, string $file, array $headers = [], array $uriParams = [])
    {
        $resource = self::tryFopen($file, 'r');
        $body = $this->client->getStreamFactory()->createStreamFromResource($resource);

        if ($body->isReadable()) {
            $headers = \array_merge([ResponseMediator::CONTENT_TYPE_HEADER => self::guessFileContentType($file)], $headers);
        }

        $response = $this->client->getHttpClient()->put(self::prepareUri($uri, $uriParams), $headers, $body);

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
    protected static function encodePath($uri): string
    {
        return \rawurlencode((string) $uri);
    }

    /**
     * @param int|string $id
     * @param string     $uri
     *
     * @return string
     */
    protected function getProjectPath($id, string $uri): string
    {
        return 'projects/'.self::encodePath($id).'/'.$uri;
    }

    /**
     * @param $id
     * @param string $uri
     * @return string
     */
    protected function getGroupPath($id, string $uri): string
    {
        return 'groups/'.self::encodePath($id).'/'.$uri;
    }

    /**
     * Create a new OptionsResolver with page and per_page options.
     *
     * @return OptionsResolver
     */
    protected function createOptionsResolver(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('page')
            ->setAllowedTypes('page', 'int')
            ->setAllowedValues('page', function ($value): bool {
                return $value > 0;
            })
        ;
        $resolver->setDefined('per_page')
            ->setAllowedTypes('per_page', 'int')
            ->setAllowedValues('per_page', function ($value): bool {
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
    private static function prepareUri(string $uri, array $query = []): string
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
    private function createMultipartStreamBuilder(array $params = [], array $files = []): MultipartStreamBuilder
    {
        $builder = new MultipartStreamBuilder($this->client->getStreamFactory());

        foreach ($params as $name => $value) {
            $builder->addResource($name, $value);
        }

        foreach ($files as $name => $file) {
            $builder->addResource($name, self::tryFopen($file, 'r'), [
                'headers' => [
                    ResponseMediator::CONTENT_TYPE_HEADER => self::guessFileContentType($file),
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
    private static function prepareMultipartBody(MultipartStreamBuilder $builder): StreamInterface
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
    private static function addMultipartContentType(array $headers, MultipartStreamBuilder $builder): array
    {
        $contentType = \sprintf('%s; boundary=%s', ResponseMediator::MULTIPART_CONTENT_TYPE, $builder->getBoundary());

        return \array_merge([ResponseMediator::CONTENT_TYPE_HEADER => $contentType], $headers);
    }

    /**
     * Prepare the request JSON body.
     *
     * @param array<string,mixed> $params
     *
     * @return string|null
     */
    private static function prepareJsonBody(array $params): ?string
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
    private static function addJsonContentType(array $headers): array
    {
        return \array_merge([ResponseMediator::CONTENT_TYPE_HEADER => ResponseMediator::JSON_CONTENT_TYPE], $headers);
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
     * @throws RuntimeException if the file cannot be opened
     *
     * @return resource
     *
     * @see https://github.com/guzzle/psr7/blob/1.6.1/src/functions.php#L287-L320
     */
    private static function tryFopen(string $filename, string $mode)
    {
        $ex = null;
        \set_error_handler(function () use ($filename, $mode, &$ex): void {
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
    private static function guessFileContentType(string $file): string
    {
        if (!\class_exists(\finfo::class, false)) {
            return ResponseMediator::STREAM_CONTENT_TYPE;
        }

        $finfo = new \finfo(\FILEINFO_MIME_TYPE);
        $type = $finfo->file($file);

        return false !== $type ? $type : ResponseMediator::STREAM_CONTENT_TYPE;
    }
}
