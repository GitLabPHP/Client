<?php namespace Gitlab\Model;

use Gitlab\Client;
use Gitlab\Exception\RuntimeException;
use Gitlab\Api\AbstractApi;

abstract class AbstractModel
{
    /**
     * @var array
     */
    protected static $properties;

    /**
     * @var array
     */
    protected $data = array();

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
     * @param Client $client
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
     * @param string $api
     * @return AbstractApi
     */
    public function api($api)
    {
        return $this->getClient()->api($api);
    }

    /**
     * @param array $data
     * @return $this
     */
    public function hydrate(array $data = array())
    {
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (in_array($k, static::$properties)) {
                    $this->$k = $v;
                }
            }
        }

        return $this;
    }

    /**
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        if (!in_array($property, static::$properties)) {
            throw new RuntimeException(sprintf(
                'Property "%s" does not exist for %s object', $property, get_called_class()
            ));
        }

        $this->data[$property] = $value;
    }

    /**
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        if (!in_array($property, static::$properties)) {
            throw new RuntimeException(sprintf(
                'Property "%s" does not exist for %s object',
                $property, get_called_class()
            ));
        }

        if (isset($this->data[$property])) {
            return $this->data[$property];
        }

        return null;
    }
}
