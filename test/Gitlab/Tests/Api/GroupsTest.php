<?php namespace Gitlab\Tests\Api;

use Gitlab\Api\AbstractApi;

class GroupsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllGroups()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'A group'),
            array('id' => 2, 'name' => 'Another group'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups', array('page' => 1, 'per_page' => 10))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(['page' => 1, 'per_page' => 10]));
    }

    /**
     * @test
     */
    public function shouldGetAllGroupsWithBooleanParam()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'A group'),
            array('id' => 2, 'name' => 'Another group'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups', ['all_available' => 'false'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(['all_available' => false]));
    }

    /**
     * @test
     */
    public function shouldGetAllGroupProjectsWithBooleanParam()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'A group'),
            array('id' => 2, 'name' => 'Another group'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/projects', ['archived' => 'false'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->projects(1, ['archived' => false]));
    }

    /**
     * @test
     */
    public function shouldNotNeedPaginationWhenGettingGroups()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'A group'),
            array('id' => 2, 'name' => 'Another group'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups', array())
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldShowGroup()
    {
        $expectedArray = array('id' => 1, 'name' => 'A group');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1));
    }

    /**
     * @test
     */
    public function shouldCreateGroup()
    {
        $expectedArray = array('id' => 1, 'name' => 'A new group');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups', array('name' => 'A new group', 'path' => 'a-new-group', 'visibility' => 'private'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create('A new group', 'a-new-group'));
    }

    /**
     * @test
     */
    public function shouldCreateGroupWithDescriptionAndVisibility()
    {
        $expectedArray = array('id' => 1, 'name' => 'A new group', 'visibility_level' => 2);

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups', array('name' => 'A new group', 'path' => 'a-new-group', 'description' => 'Description', 'visibility' => 'public'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create('A new group', 'a-new-group', 'Description', 'public'));
    }

    /**
     * @test
     */
    public function shouldCreateGroupWithDescriptionVisibilityAndParentId()
    {
        $expectedArray = array('id' => 1, 'name' => 'A new group', 'visibility_level' => 2, 'parent_id' => 666);

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups', array('name' => 'A new group', 'path' => 'a-new-group', 'description' => 'Description', 'visibility' => 'public', 'parent_id' => 666))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create('A new group', 'a-new-group', 'Description', 'public', null, null, 666));
    }

    /**
     * @test
     */
    public function shouldUpdateGroup()
    {
        $expectedArray = array('id' => 3, 'name' => 'Group name', 'path' => 'group-path');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('groups/3', array('name' => 'Group name', 'path' => 'group-path'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(3, array('name' => 'Group name', 'path' => 'group-path')));
    }

    /**
     * @test
     */
    public function shouldTransferProjectToGroup()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups/1/projects/2')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->transfer(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetAllMembers()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'Matt'),
            array('id' => 2, 'name' => 'Bob')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/members/all/')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->allMembers(1));
    }

    /**
     * @test
     */
    public function shouldGetAllMembersUserID()
    {
        $expectedArray = array('id' => 2, 'name' => 'Bob');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/members/all/2')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->allMembers(1,2));
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
            ->with('groups/1/members')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->members(1));
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
            ->with('groups/1/members', array('user_id' => 2, 'access_level' => 3))
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
            ->with('groups/1/members/2', array('access_level' => 4))
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
            ->with('groups/1/members/2')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeMember(1, 2));
    }

    /**
     * @test
     */
    public function shouldRemoveGroup()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('groups/1')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->remove(1));
    }

    /**
     * @test
     */
    public function shouldGetAllSubgroups()
    {
        $expectedArray = array(
            array('id' => 101, 'name' => 'A subgroup'),
            array('id' => 1-2, 'name' => 'Another subggroup'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/subgroups', array('page' => 1, 'per_page' => 10))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->subgroups(1, ['page' => 1, 'per_page' => 10]));
    }

    /**
     * @test
     */
    public function shouldGetLabels()
    {
        $expectedArray = array(
            array('id' => 987, 'name' => 'bug', 'color' => '#000000'),
            array('id' => 123, 'name' => 'feature', 'color' => '#ff0000')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/labels')
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
            ->with('groups/1/labels', array('name' => 'wont-fix', 'color' => '#ffffff'))
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
            ->with('groups/1/labels', array('name' => 'bug', 'new_name' => 'big-bug', 'color' => '#00ffff'))
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
            ->with('groups/1/labels', array('name' => 'bug'))
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeLabel(1, 'bug'));
    }

    public function shouldGetVariables()
    {
        $expectedArray = array(
            array('key' => 'ftp_username', 'value' => 'ftp'),
            array('key' => 'ftp_password', 'value' => 'somepassword')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/variables')
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
            ->with('groups/1/variables/ftp_username')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->variable(1, 'ftp_username'));
    }

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
            ->with('groups/1/variables', $expectedArray)
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
            ->with('groups/1/variables', $expectedArray)
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true));
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
            ->with('groups/1/variables/'.$expectedKey, array('value' => $expectedValue))
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
            ->with('groups/1/variables/DEPLOY_SERVER', array('value' => 'stage.example.com', 'protected' => true))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true));
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
            ->with('groups/1/variables/ftp_password')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeVariable(1, 'ftp_password'));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\Groups';
    }

    /**
     * @test
     */
    public function shouldGetAllGroupProjectsWithIssuesEnabled()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'A group', 'issues_enabled' => true),
            array('id' => 2, 'name' => 'Another group', 'issues_enabled' => true),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/projects', ['with_issues_enabled' => 'true'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->projects(1, ['with_issues_enabled' => true]));
    }

    /**
     * @test
     */
    public function shouldGetAllGroupProjectsWithMergeRequestsEnabled()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'A group', 'merge_requests_enabled' => true),
            array('id' => 2, 'name' => 'Another group', 'merge_requests_enabled' => true),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/projects', ['with_merge_requests_enabled' => 'true'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->projects(1, ['with_merge_requests_enabled' => true]));
    }

    /**
     * @test
     */
    public function shouldGetAllGroupProjectsSharedToGroup()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'A project', 'shared_with_groups' => [1]),
            array('id' => 2, 'name' => 'Another project', 'shared_with_groups' => [1]),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/projects', ['with_shared' => 'true'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->projects(1, ['with_shared' => true]));
    }

    /**
     * @test
     */
    public function shouldGetAllGroupProjectsIncludingSubsgroups()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'A project'),
            array('id' => 2, 'name' => 'Another project', 'shared_with_groups' => [1]),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/projects', ['include_subgroups' => 'true'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->projects(1, ['include_subgroups' => true]));
    }

    /**
     * @test
     */
    public function shouldGetAllGroupProjectsIncludingCustomAttributes()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'A project', 'custom_Attr' => true),
            array('id' => 2, 'name' => 'Another project', 'custom_Attr' => true),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/projects', ['with_custom_attributes' => 'true'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->projects(1, ['with_custom_attributes' => true]));
    }
}
