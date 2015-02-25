<?php namespace Gitlab\Tests\Api;

abstract class ApiTestCase extends TestCase
{
    abstract protected function getApiClass();

    /**
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject|mixed
     */
    protected function getApiMock($methods = array())
    {
        $client = $this->getClientMock();

        $methods = array_merge(array('get', 'post', 'postRaw', 'patch', 'delete', 'put', 'head'), $methods);

        return $this->getMockBuilder($this->getApiClass())
            ->setMethods($methods)
            ->setConstructorArgs(array($client))
            ->getMock()
        ;
    }
}
