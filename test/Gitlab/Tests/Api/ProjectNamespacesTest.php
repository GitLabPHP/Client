<?php namespace Gitlab\Tests\Api;

use Gitlab\Api\AbstractApi;

class ProjectNamespacesTest extends ApiTestCase
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
            ->with('namespaces', array('page' => 1, 'per_page' => 10))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1, 10));
    }

    /**
     * @test
     */
    public function shouldNotNeedPaginationWhenGettingNamespaces()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'bespokes'),
            array('id' => 2, 'name' => 'internal')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('namespaces', array('page' => 1, 'per_page' => AbstractApi::PER_PAGE))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all());
    }
    /**
     * @test
     */
    public function shouldSearchNamespaces()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'bespokes'),
            array('id' => 2, 'name' => 'internal')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('namespaces', array('search' => 'term', 'page' => 1, 'per_page' => 10))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->search('term', 1, 10));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\ProjectNamespaces';
    }
}
