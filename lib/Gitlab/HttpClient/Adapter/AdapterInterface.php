<?php
namespace Gitlab\HttpClient\Adapter;

interface AdapterInterface
{
    public function request($path, array $parameters = array(), $httpMethod = 'GET', array $headers = array());

    public function authenticate($token, $authMethod, $sudo = null);
}
