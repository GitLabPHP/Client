<?php namespace Gitlab\Api;

use Gitlab\Client;
use Gitlab\HttpClient\Message\QueryStringBuilder;
use Gitlab\HttpClient\Message\ResponseMediator;
use Gitlab\Tests\HttpClient\Message\QueryStringBuilderTest;
use Http\Discovery\StreamFactoryDiscovery;
use Http\Message\MultipartStream\MultipartStreamBuilder;
use Http\Message\StreamFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Abstract class for Api classes
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Matt Humphrey <matt@m4tt.co>
 * @author Radu Topala <radu.topala@trisoft.ro>
 */
abstract class AbstractApi implements ApiInterface
{
    /**
     * The client
     *
     * @var Client
     */
    protected $client;

    /**
     * @var StreamFactory
     */
    private $streamFactory;

    /**
     * @param Client $client
     * @param StreamFactory|null $streamFactory
     */
    public function __construct(Client $client, StreamFactory $streamFactory = null)
    {
        $this->client = $client;
        $this->streamFactory = $streamFactory ?: StreamFactoryDiscovery::find();
    }

    /**
     * @return $this
     * @codeCoverageIgnore
     */
    public function configure()
    {
        return $this;
    }

    /**
     * Performs a GET query and returns the response as a PSR-7 response object.
     *
     * @param string $path
     * @param array $parameters
     * @param array $requestHeaders
     * @return ResponseInterface
     */
    protected function getAsResponse($path, array $parameters = array(), $requestHeaders = array())
    {
        $path = $this->preparePath($path, $parameters);

        return $this->client->getHttpClient()->get($path, $requestHeaders);
    }

    /**
     * @param string $path
     * @param array $parameters
     * @param array $requestHeaders
     * @return mixed
     */
    protected function get($path, array $parameters = array(), $requestHeaders = array())
    {
        return ResponseMediator::getContent($this->getAsResponse($path, $parameters, $requestHeaders));
    }

    /**
     * @param string $path
     * @param array $parameters
     * @param array $requestHeaders
     * @param array $files
     * @return mixed
     */
    protected function post($path, array $parameters = array(), $requestHeaders = array(), array $files = array())
    {
        $path = $this->preparePath($path);

        $body = null;
        if (empty($files) && !empty($parameters)) {
            $body = $this->prepareBody($parameters);
            $requestHeaders['Content-Type'] = 'application/x-www-form-urlencoded';
        } elseif (!empty($files)) {
            $builder = new MultipartStreamBuilder($this->streamFactory);

            foreach ($parameters as $name => $value) {
                $builder->addResource($name, $value);
            }

            foreach ($files as $name => $file) {
                $builder->addResource($name, fopen($file, 'r'), [
                    'headers' => [
                        'Content-Type' => $this->guessContentType($file),
                    ],
                    'filename' => basename($file),
                ]);
            }

            $body = $builder->build();
            $requestHeaders['Content-Type'] = 'multipart/form-data; boundary='.$builder->getBoundary();
        }

        $response = $this->client->getHttpClient()->post($path, $requestHeaders, $body);

        return ResponseMediator::getContent($response);
    }

    /**
     * @param string $path
     * @param array $parameters
     * @param array $requestHeaders
     * @return mixed
     */
    protected function put($path, array $parameters = array(), $requestHeaders = array(), array $files = array())
    {
        $path = $this->preparePath($path);

        $body = null;
        if (empty($files) && !empty($parameters)) {
            $body = $this->prepareBody($parameters);
            $requestHeaders['Content-Type'] = 'application/x-www-form-urlencoded';
        } elseif (!empty($files)) {
            $builder = new MultipartStreamBuilder($this->streamFactory);

            foreach ($parameters as $name => $value) {
                $builder->addResource($name, $value);
            }

            foreach ($files as $name => $file) {
                $builder->addResource($name, fopen($file, 'r'), [
                    'headers' => [
                        'Content-Type' => $this->guessContentType($file),
                    ],
                    'filename' => basename($file),
                ]);
            }

            $body = $builder->build();
            $requestHeaders['Content-Type'] = 'multipart/form-data; boundary='.$builder->getBoundary();
        }

        $response = $this->client->getHttpClient()->put($path, $requestHeaders, $body);

        return ResponseMediator::getContent($response);
    }

    /**
     * @param string $path
     * @param array $parameters
     * @param array $requestHeaders
     * @return mixed
     */
    protected function delete($path, array $parameters = array(), $requestHeaders = array())
    {
        $path = $this->preparePath($path, $parameters);

        $response = $this->client->getHttpClient()->delete($path, $requestHeaders);

        return ResponseMediator::getContent($response);
    }

    /**
     * @param int $id
     * @param string $path
     * @return string
     */
    protected function getProjectPath($id, $path)
    {
        return 'projects/'.$this->encodePath($id).'/'.$path;
    }

    /**
     * @param int $id
     * @param string $path
     * @return string
     */
    protected function getGroupPath($id, $path)
    {
        return 'groups/'.$this->encodePath($id).'/'.$path;
    }

    /**
     * @param string $path
     * @return string
     */
    protected function encodePath($path)
    {
        $path = rawurlencode($path);

        return str_replace('.', '%2E', $path);
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
     * @return StreamInterface
     */
    private function prepareBody(array $parameters = [])
    {
        $raw = QueryStringBuilder::build($parameters);
        $stream = $this->streamFactory->createStream($raw);

        return $stream;
    }

    private function preparePath($path, array $parameters = [])
    {
        if (count($parameters) > 0) {
            $path .= '?'.QueryStringBuilder::build($parameters);
        }

        return $path;
    }

    /**
     * @param $file
     *
     * @return string
     */
    private function guessContentType($file)
    {
        if (!class_exists(\finfo::class, false)) {
            return 'application/octet-stream';
        }
        $finfo = new \finfo(FILEINFO_MIME_TYPE);

        return $finfo->file($file);
    }
}
