<?php

namespace Gitlab\Api;

use Gitlab\Client;
use Gitlab\Exception\RuntimeException;
use Gitlab\HttpClient\Message\ResponseMediator;
use Gitlab\HttpClient\Util\QueryStringBuilder;
use Http\Message\MultipartStream\MultipartStreamBuilder;
use Http\Message\StreamFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Abstract class for Api classes.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Matt Humphrey <matt@m4tt.co>
 * @author Radu Topala <radu.topala@trisoft.ro>
 */
abstract class AbstractApi implements ApiInterface
{
    /**
     * The client instance.
     *
     * @var Client
     */
    protected $client;

    /**
     * The HTTP stream factory.
     *
     * @var StreamFactory
     */
    private $streamFactory;

    /**
     * @param Client             $client
     * @param StreamFactory|null $streamFactory @deprecated since version 9.18 and will be removed in 10.0.
     *
     * @return void
     */
    public function __construct(Client $client, StreamFactory $streamFactory = null)
    {
        $this->client = $client;

        if (null === $streamFactory) {
            $this->streamFactory = $client->getStreamFactory();
        } else {
            @\trigger_error(\sprintf('The %s() method\'s $streamFactory parameter is deprecated since version 9.18 and will be removed in 10.0.', __METHOD__), E_USER_DEPRECATED);
            $this->streamFactory = $streamFactory;
        }
    }

    /**
     * @return $this
     *
     * @deprecated since version 9.18 and will be removed in 10.0.
     */
    public function configure()
    {
        @\trigger_error(\sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0.', __METHOD__), E_USER_DEPRECATED);

        return $this;
    }

    /**
     * Performs a GET query and returns the response as a PSR-7 response object.
     *
     * @param string               $path
     * @param array<string,mixed>  $parameters
     * @param array<string,string> $requestHeaders
     *
     * @return ResponseInterface
     */
    protected function getAsResponse($path, array $parameters = [], array $requestHeaders = [])
    {
        $path = $this->preparePath($path, $parameters);

        return $this->client->getHttpClient()->get($path, $requestHeaders);
    }

    /**
     * @param string               $path
     * @param array<string,mixed>  $parameters
     * @param array<string,string> $requestHeaders
     *
     * @return mixed
     */
    protected function get($path, array $parameters = [], array $requestHeaders = [])
    {
        return ResponseMediator::getContent($this->getAsResponse($path, $parameters, $requestHeaders));
    }

    /**
     * @param string               $path
     * @param array<string,mixed>  $parameters
     * @param array<string,string> $requestHeaders
     * @param array<string,string> $files
     *
     * @return mixed
     */
    protected function post($path, array $parameters = [], array $requestHeaders = [], array $files = [])
    {
        $path = $this->preparePath($path);

        $body = null;
        if (0 === \count($files) && 0 < \count($parameters)) {
            $body = $this->prepareBody($parameters);
            $requestHeaders['Content-Type'] = 'application/x-www-form-urlencoded';
        } elseif (0 < \count($files)) {
            $builder = new MultipartStreamBuilder($this->streamFactory);

            foreach ($parameters as $name => $value) {
                $builder->addResource($name, $value);
            }

            foreach ($files as $name => $file) {
                $builder->addResource($name, self::tryFopen($file, 'r'), [
                    'headers' => [
                        'Content-Type' => $this->guessContentType($file),
                    ],
                    'filename' => \basename($file),
                ]);
            }

            $body = $builder->build();
            $requestHeaders['Content-Type'] = 'multipart/form-data; boundary='.$builder->getBoundary();
        }

        $response = $this->client->getHttpClient()->post($path, $requestHeaders, $body);

        return ResponseMediator::getContent($response);
    }

    /**
     * @param string               $path
     * @param array<string,mixed>  $parameters
     * @param array<string,string> $requestHeaders
     * @param array<string,string> $files
     *
     * @return mixed
     */
    protected function put($path, array $parameters = [], array $requestHeaders = [], array $files = [])
    {
        $path = $this->preparePath($path);

        $body = null;
        if (0 === \count($files) && 0 < \count($parameters)) {
            $body = $this->prepareBody($parameters);
            $requestHeaders['Content-Type'] = 'application/x-www-form-urlencoded';
        } elseif (0 < \count($files)) {
            $builder = new MultipartStreamBuilder($this->streamFactory);

            foreach ($parameters as $name => $value) {
                $builder->addResource($name, $value);
            }

            foreach ($files as $name => $file) {
                $builder->addResource($name, self::tryFopen($file, 'r'), [
                    'headers' => [
                        'Content-Type' => $this->guessContentType($file),
                    ],
                    'filename' => \basename($file),
                ]);
            }

            $body = $builder->build();
            $requestHeaders['Content-Type'] = 'multipart/form-data; boundary='.$builder->getBoundary();
        }

        $response = $this->client->getHttpClient()->put($path, $requestHeaders, $body);

        return ResponseMediator::getContent($response);
    }

    /**
     * @param string               $path
     * @param array<string,mixed>  $parameters
     * @param array<string,string> $requestHeaders
     *
     * @return mixed
     */
    protected function delete($path, array $parameters = [], array $requestHeaders = [])
    {
        $path = $this->preparePath($path, $parameters);

        $response = $this->client->getHttpClient()->delete($path, $requestHeaders);

        return ResponseMediator::getContent($response);
    }

    /**
     * @param int|string $id
     * @param string     $path
     *
     * @return string
     */
    protected function getProjectPath($id, $path)
    {
        return 'projects/'.$this->encodePath($id).'/'.$path;
    }

    /**
     * @param int|string $id
     * @param string     $path
     *
     * @return string
     */
    protected function getGroupPath($id, $path)
    {
        return 'groups/'.$this->encodePath($id).'/'.$path;
    }

    /**
     * @param int|string $path
     *
     * @return string
     */
    protected function encodePath($path)
    {
        $path = \rawurlencode((string) $path);

        return \str_replace('.', '%2E', $path);
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
     * @param array $parameters
     *
     * @return StreamInterface
     */
    private function prepareBody(array $parameters = [])
    {
        $parameters = \array_filter($parameters, function ($value) {
            return null !== $value;
        });

        $raw = QueryStringBuilder::build($parameters);

        return $this->streamFactory->createStream($raw);
    }

    /**
     * @param string $path
     * @param array  $parameters
     *
     * @return string
     */
    private function preparePath($path, array $parameters = [])
    {
        $parameters = \array_filter($parameters, function ($value) {
            return null !== $value;
        });

        if (\count($parameters) > 0) {
            $path .= '?'.QueryStringBuilder::build($parameters);
        }

        return $path;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    private function guessContentType($file)
    {
        if (!\class_exists(\finfo::class, false)) {
            return 'application/octet-stream';
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $type = $finfo->file($file);

        return false !== $type ? $type : 'application/octet-stream';
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
}
