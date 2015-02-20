<?php namespace Gitlab\Tests\Api;

use Gitlab\Api\AbstractApi;

class ProjectsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllProjects()
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects/all', $expectedArray);

        $this->assertEquals($expectedArray, $api->all(1, 10));
    }

    /**
     * @test
     */
    public function shouldNotNeedPaginationWhenGettingProjects()
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/all', array('page' => 1, 'per_page' => AbstractApi::PER_PAGE))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldGetAccessibleProjects()
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, 2, 7);

        $this->assertEquals($expectedArray, $api->accessible(2, 7));
    }

    /**
     * @test
     */
    public function shouldGetOwnedProjects()
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects/owned', $expectedArray, 3, 50);

        $this->assertEquals($expectedArray, $api->owned(3, 50));
    }

    /**
     * @test
     */
    public function shouldSearchProjects()
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects/search/a+project', $expectedArray);

        $this->assertEquals($expectedArray, $api->search('a project', 1, 10));
    }

    /**
     * @test
     */
    public function shouldShowProject()
    {
        $expectedArray = array('id' => 1, 'name' => 'Project Name');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1));
    }

    /**
     * @test
     */
    public function shouldCreateProject()
    {
        $expectedArray = array('id' => 1, 'name' => 'Project Name');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects', array('name' => 'Project Name', 'issues_enabled' => true))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create('Project Name', array(
            'issues_enabled' => true
        )));
    }

    protected function getMultipleProjectsRequestMock($path, $expectedArray = array(), $page = 1, $per_page = 10)
    {
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with($path, array('page' => $page, 'per_page' => $per_page))
            ->will($this->returnValue($expectedArray))
        ;

        return $api;
    }

    protected function getMultipleProjectsData()
    {
        return array(
            array('id' => 1, 'name' => 'A project'),
            array('id' => 2, 'name' => 'Another project')
        );
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\Projects';
    }
} 