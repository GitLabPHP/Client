<?php

namespace Gitlab\Model;

use Gitlab\Client;
use Gitlab\Exception\RuntimeException;

abstract class AbstractModel
{
    protected static $_properties;

    protected $_data = array();
    protected $_client = null;

    public function getClient()
    {
        return $this->_client;
    }

    public function setClient(Client $client = null)
    {
        if (null !== $client) {
            $this->_client = $client;
        }

        return $this;
    }

    public function api($api)
    {
        return $this->getClient()->api($api);
    }

    public function hydrate(array $data = array())
    {
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $this->$k = $v;
            }
        }

        return $this;
    }

    public function __set($property, $value)
    {
        if (!in_array($property, static::$_properties)) {
            throw new RuntimeException(sprintf(
                'Property "%s" does not exist for %s object', $property, get_called_class()
            ));
        }

        $this->_data[$property] = $value;
    }

    public function __get($property)
    {
        if (!in_array($property, static::$_properties)) {
            throw new RuntimeException(sprintf(
                'Property "%s" does not exist for %s object',
                $property, get_called_class()
            ));
        }

        if (isset($this->_data[$property])) {
            return $this->_data[$property];
        }

        return null;
    }

}
