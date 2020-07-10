<?php

namespace Gitlab\Model;

use Gitlab\Api\AbstractApi;
use Gitlab\Client;
use Gitlab\Exception\InvalidArgumentException;
use Gitlab\Exception\RuntimeException;

abstract class AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties;

    /**
     * @var array<string,mixed>
     */
    protected $data = [];

    /**
     * @var Client
     */
    protected $client;

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param Client|null $client
     *
     * @return $this
     */
    public function setClient(Client $client = null)
    {
        if (null !== $client) {
            $this->client = $client;
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return AbstractApi|mixed
     *
     * @throws InvalidArgumentException
     *
     * @deprecated since version 9.18 and will be removed in 10.0. Use the direct methods instead.
     */
    public function api($name)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0. Use the direct methods instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->getClient()->api($name);
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return $this
     */
    protected function hydrate(array $data = [])
    {
        foreach ($data as $field => $value) {
            $this->setData($field, $value);
        }

        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    protected function setData($field, $value)
    {
        if (in_array($field, static::$properties, true)) {
            $this->data[$field] = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $property
     * @param mixed  $value
     *
     * @return void
     *
     * @throws RuntimeException
     */
    public function __set($property, $value)
    {
        throw new RuntimeException('Model properties are immutable');
    }

    /**
     * @param string $property
     *
     * @return mixed
     *
     * @throws RuntimeException
     */
    public function __get($property)
    {
        if (!in_array($property, static::$properties, true)) {
            throw new RuntimeException(sprintf('Property "%s" does not exist for %s object', $property, get_called_class()));
        }

        if (isset($this->data[$property])) {
            return $this->data[$property];
        }

        return null;
    }

    /**
     * @param string $property
     *
     * @return bool
     */
    public function __isset($property)
    {
        return isset($this->data[$property]);
    }
}
