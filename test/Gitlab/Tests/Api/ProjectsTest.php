<?php namespace Gitlab\Tests\Api;

use Gitlab\Api\AbstractApi;
use Gitlab\Api\Projects;

class ProjectsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllProjects()
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects/all', $expectedArray);

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldGetAllProjectsSortedByName()
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects/all', $expectedArray, 1, 5, 'name', 'asc');

        $this->assertEquals($expectedArray, $api->all(1, 5, 'name'));
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
            ->with('projects/all', array('page' => 1, 'per_page' => AbstractApi::PER_PAGE, 'order_by' => Projects::ORDER_BY, 'sort' => Projects::SORT))
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

        $this->assertEquals($expectedArray, $api->search('a project'));
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

    /**
     * @test
     */
    public function shouldCreateProjectForUser()
    {
        $expectedArray = array('id' => 1, 'name' => 'Project Name');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/user/1', array('name' => 'Project Name', 'issues_enabled' => true))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createForUser(1, 'Project Name', array(
            'issues_enabled' => true
        )));
    }

    /**
     * @test
     */
    public function shouldRemoveProject()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->remove(1));
    }

    /**
     * @test
     */
    public function shouldGetMembers()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'Matt'),
            array('id' => 2, 'name' => 'Bob')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/members')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->members(1));
    }

    /**
     * @test
     */
    public function shouldGetMembersWithQuery()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'Matt')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/members', array('query' => 'at'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->members(1, 'at'));
    }

    /**
     * @test
     */
    public function shouldGetMember()
    {
        $expectedArray = array('id' => 2, 'name' => 'Matt');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/members/2')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->member(1, 2));
    }

    /**
     * @test
     */
    public function shouldAddMember()
    {
        $expectedArray = array('id' => 1, 'name' => 'Matt');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/members', array('user_id' => 2, 'access_level' => 3))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addMember(1, 2, 3));
    }

    /**
     * @test
     */
    public function shouldSaveMember()
    {
        $expectedArray = array('id' => 1, 'name' => 'Matt');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/members/2', array('access_level' => 4))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->saveMember(1, 2, 4));
    }

    /**
     * @test
     */
    public function shouldRemoveMember()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/members/2')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeMember(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetHooks()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'Test hook'),
            array('id' => 2, 'name' => 'Another hook'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/hooks')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->hooks(1));
    }

    /**
     * @test
     */
    public function shouldGetHook()
    {
        $expectedArray = array('id' => 2, 'name' => 'Another hook');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/hooks/2')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->hook(1, 2));
    }

    /**
     * @test
     */
    public function shouldAddHook()
    {
        $expectedArray = array('id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/hooks', array(
                'url' => 'http://www.example.com',
                'push_events' => true,
                'issues_events' => false,
                'merge_requests_events' => false,
                'tag_push_events' => false
            ))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addHook(1, 'http://www.example.com'));
    }

    /**
     * @test
     */
    public function shouldAddHookWithoutPushEvents()
    {
        $expectedArray = array('id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/hooks', array(
                'url' => 'http://www.example.com',
                'push_events' => false,
                'issues_events' => false,
                'merge_requests_events' => false,
                'tag_push_events' => false
            ))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addHook(1, 'http://www.example.com', false));
    }

    /**
     * @test
     */
    public function shouldAddHookWithIssuesEvents()
    {
        $expectedArray = array('id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/hooks', array(
                'url' => 'http://www.example.com',
                'push_events' => false,
                'issues_events' => true,
                'merge_requests_events' => false,
                'tag_push_events' => false
            ))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addHook(1, 'http://www.example.com', false, true));
    }

    /**
     * @test
     */
    public function shouldAddHookWithMergeRequestEvents()
    {
        $expectedArray = array('id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/hooks', array(
                'url' => 'http://www.example.com',
                'push_events' => false,
                'issues_events' => false,
                'merge_requests_events' => true,
                'tag_push_events' => false
            ))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addHook(1, 'http://www.example.com', false, false, true));
    }

    /**
     * @test
     */
    public function shouldAddHookWithTagPushEvents()
    {
        $expectedArray = array('id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/hooks', array(
                'url' => 'http://www.example.com',
                'push_events' => false,
                'issues_events' => false,
                'merge_requests_events' => false,
                'tag_push_events' => true
            ))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addHook(1, 'http://www.example.com', false, false, false, true));
    }

    /**
     * @test
     */
    public function shouldUpdateHookUrlOnly()
    {
        $expectedArray = array('id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/hooks/3', array('url' => 'http://www.example-test.com'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateHook(1, 3, array('url' => 'http://www.example-test.com')));
    }

    /**
     * @test
     */
    public function shouldUpdateHookWithPushEvents()
    {
        $expectedArray = array('id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/hooks/3', array('url' => 'http://www.example-test.com', 'push_events' => true))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateHook(1, 3, array('url' => 'http://www.example-test.com', 'push_events' => true)));
    }

    /**
     * @test
     */
    public function shouldUpdateHookWithDifferentEvents()
    {
        $expectedArray = array('id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/hooks/3', array(
                'url' => 'http://www.example-test.com',
                'issues_events' => true,
                'merge_requests_events' => true,
                'tag_push_events' => true
            ))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateHook(1, 3, 'http://www.example-test.com', false, true, true, true));
    }

    /**
     * @test
     */
    public function shouldRemoveHook()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/hooks/2')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeHook(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetKeys()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'test-key'),
            array('id' => 2, 'title' => 'another-key')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/keys')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->keys(1));
    }

    /**
     * @test
     */
    public function shouldGetKey()
    {
        $expectedArray = array('id' => 2, 'title' => 'another-key');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/keys/2')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->key(1, 2));
    }

    /**
     * @test
     */
    public function shouldAddKey()
    {
        $expectedArray = array('id' => 3, 'title' => 'new-key');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/keys', array('title' => 'new-key', 'key' => '...'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addKey(1, 'new-key', '...'));
    }

    /**
     * @test
     */
    public function shouldRemoveKey()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/keys/3')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeKey(1, 3));
    }

    /**
     * @test
     */
    public function shouldGetEvents()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'An event'),
            array('id' => 2, 'title' => 'Another event')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/events')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->events(1));
    }

    /**
     * @test
     */
    public function shouldGetLabels()
    {
        $expectedArray = array(
            array('name' => 'bug', 'color' => '#000000'),
            array('name' => 'feature', 'color' => '#ff0000')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/labels')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->labels(1));
    }

    /**
     * @test
     */
    public function shouldAddLabel()
    {
        $expectedArray = array('name' => 'bug', 'color' => '#000000');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/labels', array('name' => 'wont-fix', 'color' => '#ffffff'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addLabel(1, array('name' => 'wont-fix', 'color' => '#ffffff')));
    }

    /**
     * @test
     */
    public function shouldUpdateLabel()
    {
        $expectedArray = array('name' => 'bug', 'color' => '#00ffff');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/labels', array('name' => 'bug', 'new_name' => 'big-bug', 'color' => '#00ffff'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateLabel(1, array('name' => 'bug', 'new_name' => 'big-bug', 'color' => '#00ffff')));
    }

    /**
     * @test
     */
    public function shouldRemoveLabel()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/labels', array('name' => 'bug'))
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeLabel(1, 'bug'));
    }

    /**
     * @test
     */
    public function shouldCreateForkRelation()
    {
        $expectedArray = array('project_id' => 1, 'forked_id' => 2);

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/fork/2')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createForkRelation(1, 2));
    }

    /**
     * @test
     */
    public function shouldRemoveForkRelation()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/2/fork')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeForkRelation(2));
    }

    /**
     * @test
     */
    public function shouldSetService()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/services/hipchat', array('param' => 'value'))
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->setService(1, 'hipchat', array('param' => 'value')));
    }

    /**
     * @test
     */
    public function shouldRemoveService()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/services/hipchat')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeService(1, 'hipchat'));
    }

    protected function getMultipleProjectsRequestMock($path, $expectedArray = array(), $page = 1, $per_page = 20, $order_by = 'created_at', $sort = 'asc')
    {
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with($path, array('page' => $page, 'per_page' => $per_page, 'order_by' => $order_by, 'sort' => $sort))
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
