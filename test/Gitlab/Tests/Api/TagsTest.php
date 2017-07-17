<?php namespace Gitlab\Tests\Api;

class TagsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllTags()
    {
        $expectedArray = array(
            array('name' => 'v1.0.0'),
            array('name' => 'v1.1.0'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/tags')
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->all(1));
    }

    /**
     * @test
     */
    public function shouldShowTag()
    {
        $expectedArray = array(
            array('name' => 'v1.0.0'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/tags/v1.0.0')
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->show(1, 'v1.0.0'));
    }

    /**
     * @test
     */
    public function shouldCreateTag()
    {
        $expectedArray = array(
            array('name' => 'v1.1.0'),
        );

        $params = array(
            'id'       => 1,
            'tag_name' => 'v1.1.0',
            'ref'      => 'ref/heads/master'
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/tags', $params)
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->create(1, $params));
    }

    /**
     * @test
     */
    public function shouldRemoveTag()
    {
        $expectedArray = array(
            array('name' => 'v1.1.0'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/repository/tags/v1.1.0')
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->remove(1, 'v1.1.0'));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\Tags';
    }
}
