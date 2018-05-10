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

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray);

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldGetAllProjectsSortedByName()
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['page' => 1, 'per_page' => 5, 'order_by' => 'name', 'sort' => 'asc']);

        $this->assertEquals($expectedArray, $api->all(['page' => 1, 'per_page' => 5, 'order_by' => 'name', 'sort' => 'asc']));
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
            ->with('projects')
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

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray);

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldGetOwnedProjects()
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['owned' => 'true']);

        $this->assertEquals($expectedArray, $api->all(['owned' => true]));
    }

    /**
     * @test
     */
    public function shouldGetNotArchivedProjects()
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['archived' => 'false']);

        $this->assertEquals($expectedArray, $api->all(['archived' => false]));
    }

    /**
     * @test
     */
    public function shouldSearchProjects()
    {
        $expectedArray = $this->getMultipleProjectsData();

        $api = $this->getMultipleProjectsRequestMock('projects', $expectedArray, ['search' => 'a project']);
        $this->assertEquals($expectedArray, $api->all(['search' => 'a project']));
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
    public function shouldUpdateProject()
    {
        $expectedArray = array('id' => 1, 'name' => 'Updated Name');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1', array('name' => 'Updated Name', 'issues_enabled' => true))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(1, array(
            'name' => 'Updated Name',
            'issues_enabled' => true
        )));
    }

    /**
     * @test
     */
    public function shouldArchiveProject()
    {
        $expectedArray = array('id' => 1, 'archived' => true);

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/archive')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->archive(1));
    }

    /**
     * @test
     */
    public function shouldUnarchiveProject()
    {
        $expectedArray = array('id' => 1, 'archived' => false);

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/unarchive')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->unarchive(1));
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
    public function shouldGetPipelines()
    {
        $expectedArray = array(
            array('id' => 1, 'status' => 'success','ref' => 'new-pipeline'),
            array('id' => 2, 'status' => 'failed', 'ref' => 'new-pipeline'),
            array('id' => 3, 'status' => 'pending', 'ref'=> 'test-pipeline')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipelines')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->pipelines(1));
    }

    /**
     * @test
     */
    public function shouldGetPipelinesWithBooleanParam()
    {
        $expectedArray = array(
            array('id' => 1, 'status' => 'success','ref' => 'new-pipeline'),
            array('id' => 2, 'status' => 'failed', 'ref' => 'new-pipeline'),
            array('id' => 3, 'status' => 'pending', 'ref'=> 'test-pipeline')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipelines', ['yaml_errors' => 'false'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->pipelines(1, ['yaml_errors' => false]));
    }

    /**
     * @test
     */
    public function shouldGetPipeline()
    {
        $expectedArray = array(
            array('id' => 1, 'status' => 'success','ref' => 'new-pipeline'),
            array('id' => 2, 'status' => 'failed', 'ref' => 'new-pipeline'),
            array('id' => 3, 'status' => 'pending', 'ref'=> 'test-pipeline')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipelines/3')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->pipeline(1, 3));
    }

    /**
     * @test
     */
    public function shouldCreatePipeline()
    {
        $expectedArray = array(
            array('id' => 4, 'status' => 'created', 'ref'=> 'test-pipeline')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/pipeline', array('ref' => 'test-pipeline'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createPipeline(1, 'test-pipeline'));
    }

    /**
     * @test
     */
    public function shouldRetryPipeline()
    {
        $expectedArray = array(
            array('id' => 5, 'status' => 'pending', 'ref'=> 'test-pipeline')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/pipelines/4/retry')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->retryPipeline(1, 4));
    }

    /**
     * @test
     */
    public function shouldCancelPipeline()
    {
        $expectedArray = array(
            array('id' => 6, 'status' => 'cancelled', 'ref'=> 'test-pipeline')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/pipelines/6/cancel')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->cancelPipeline(1, 6));
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
            ->with('projects/1/hooks', array('url' => 'http://www.example.com', 'push_events' => true, 'issues_events' => true, 'merge_requests_events' => true))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addHook(1, 'http://www.example.com', array('push_events' => true, 'issues_events' => true, 'merge_requests_events' => true)));
    }

    /**
     * @test
     */
    public function shouldAddHookWithOnlyUrl()
    {
        $expectedArray = array('id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/hooks', array('url' => 'http://www.example.com', 'push_events' => true))
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
            ->with('projects/1/hooks', array('url' => 'http://www.example.com', 'push_events' => false))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addHook(1, 'http://www.example.com', array('push_events' => false)));
    }

    /**
     * @test
     */
    public function shouldUpdateHook()
    {
        $expectedArray = array('id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/hooks/3', array('url' => 'http://www.example-test.com', 'push_events' => false))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateHook(1, 3, array('url' => 'http://www.example-test.com', 'push_events' => false)));
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
    public function shouldGetDeployKeys()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'test-key'),
            array('id' => 2, 'title' => 'another-key')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/deploy_keys')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->deployKeys(1));
    }

    /**
     * @test
     */
    public function shouldGetDeployKey()
    {
        $expectedArray = array('id' => 2, 'title' => 'another-key');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/deploy_keys/2')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->deployKey(1, 2));
    }

    /**
     * @test
     */
    public function shouldAddKey()
    {
        $expectedArray = array('id' => 3, 'title' => 'new-key', 'can_push' => false);

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/deploy_keys', array('title' => 'new-key', 'key' => '...', 'can_push' => false))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addDeployKey(1, 'new-key', '...'));
    }

    /**
     * @test
     */
    public function shouldAddKeyWithPushOption()
    {
        $expectedArray = array('id' => 3, 'title' => 'new-key', 'can_push' => true);

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/deploy_keys', array('title' => 'new-key', 'key' => '...', 'can_push' => true))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addDeployKey(1, 'new-key', '...', true));
    }

    /**
     * @test
     */
    public function shouldDeleteDeployKey()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/deploy_keys/3')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->deleteDeployKey(1, 3));
    }

    /**
     * @test
     */
    public function shoudEnableDeployKey()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/deploy_keys/3/enable')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->enableDeployKey(1, 3));
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
            ->with('projects/1/events', array())
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->events(1));
    }

    /**
     * @test
     */
    public function shouldGetEventsWithDateTimeParams()
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An event'],
            ['id' => 2, 'title' => 'Another event']
        ];

        $after = new \DateTime('2018-01-01 00:00:00');
        $before = new \DateTime('2018-01-31 00:00:00');

        $expectedWithArray = [
            'after' => $after->format('Y-m-d'),
            'before' => $before->format('Y-m-d'),
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/events', $expectedWithArray)
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->events(1, ['after' => $after, 'before' => $before]));
    }

    /**
     * @test
     */
    public function shouldGetEventsWithPagination()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'An event'),
            array('id' => 2, 'title' => 'Another event')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/events', array(
                'page' => 2,
                'per_page' => 15
            ))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->events(1, ['page' => 2, 'per_page' => 15]));
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
    public function shouldGetLanguages()
    {
        $expectedArray = ['php' => 100];
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->languages(1));
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

    /**
     * @test
     */
    public function shouldGetVariables()
    {
        $expectedArray = array(
            array('key' => 'ftp_username', 'value' => 'ftp'),
            array('key' => 'ftp_password', 'value' => 'somepassword')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/variables')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->variables(1));
    }

    /**
     * @test
     */
    public function shouldGetVariable()
    {
        $expectedArray = array('key' => 'ftp_username', 'value' => 'ftp');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/variables/ftp_username')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->variable(1, 'ftp_username'));
    }

    /**
     * @test
     */
    public function shouldAddVariable()
    {
        $expectedKey   = 'ftp_port';
        $expectedValue = '21';

        $expectedArray = array(
            'key'   => $expectedKey,
            'value' => $expectedValue,
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/variables', $expectedArray)
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addVariable(1, $expectedKey, $expectedValue));
    }

    /**
     * @test
     */
    public function shouldAddVariableWithProtected()
    {
        $expectedArray = array(
            'key'   => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'protected' => true,
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/variables', $expectedArray)
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true));
    }

    /**
     * @test
     */
    public function shouldAddVariableWithEnvironment()
    {
        $expectedArray = array(
            'key'   => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'environment_scope' => 'staging',
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/variables', $expectedArray)
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', null, 'staging'));
    }

    /**
     * @test
     */
    public function shouldAddVariableWithProtectionAndEnvironment()
    {
        $expectedArray = array(
            'key'   => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'protected' => true,
            'environment_scope' => 'staging',
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/variables', $expectedArray)
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true, 'staging'));
    }

    /**
     * @test
     */
    public function shouldUpdateVariable()
    {
        $expectedKey   = 'ftp_port';
        $expectedValue = '22';

        $expectedArray = array(
            'key'   => 'ftp_port',
            'value' => '22',
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/variables/'.$expectedKey, array('value' => $expectedValue))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateVariable(1, $expectedKey, $expectedValue));
    }

    /**
     * @test
     */
    public function shouldUpdateVariableWithProtected()
    {
        $expectedArray = array(
            'key'   => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'protected' => true,
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/variables/DEPLOY_SERVER', array('value' => 'stage.example.com', 'protected' => true))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true));
    }

    /**
     * @test
     */
    public function shouldUpdateVariableWithEnvironment()
    {
        $expectedArray = array(
            'key'   => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'environment_scope' => 'staging',
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/variables/DEPLOY_SERVER', array('value' => 'stage.example.com', 'environment_scope' => 'staging'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', null, 'staging'));
    }

    /**
     * @test
     */
    public function shouldUpdateVariableWithProtectedAndEnvironment()
    {
        $expectedArray = array(
            'key'   => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'protected' => true,
            'environment_scope' => 'staging',
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/variables/DEPLOY_SERVER', array('value' => 'stage.example.com', 'protected' => true, 'environment_scope' => 'staging'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true, 'staging'));
    }

    /**
     * @test
     */
    public function shouldRemoveVariable()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/variables/ftp_password')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeVariable(1, 'ftp_password'));
    }

    protected function getMultipleProjectsRequestMock($path, $expectedArray = array(), $expectedParameters = array())
    {
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with($path, $expectedParameters)
            ->will($this->returnValue($expectedArray))
        ;

        return $api;
    }

    /**
     * @test
     */
    public function shouldGetDeployments()
    {
        $expectedArray = array(
            array('id' => 1, 'sha' => '0000001'),
            array('id' => 2, 'sha' => '0000002'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/deployments', array())
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->deployments(1));
    }

    /**
     * @test
     */
    public function shouldGetDeploymentsWithPagination()
    {
        $expectedArray = array(
            array('id' => 1, 'sha' => '0000001'),
            array('id' => 2, 'sha' => '0000002'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/deployments', array(
                'page' => 2,
                'per_page' => 15
            ))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->deployments(1, ['page' => 2, 'per_page' => 15]));
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
