<?php namespace Gitlab\Tests\Api;

use Gitlab\Api\AbstractApi;

class ProjectNamespacesTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllNamespaces()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'bespokes'),
            array('id' => 2, 'name' => 'internal')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('namespaces', array())
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldShowNamespace()
    {
        $expectedArray = array('id' => 1, 'name' => 'internal');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('namespaces/1')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\ProjectNamespaces';
    }
}
