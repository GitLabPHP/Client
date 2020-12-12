<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Tests\Api;

use Gitlab\Api\Groups;

class GroupsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllGroups(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A group'],
            ['id' => 2, 'name' => 'Another group'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups', ['page' => 1, 'per_page' => 10])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(['page' => 1, 'per_page' => 10]));
    }

    /**
     * @test
     */
    public function shouldGetAllGroupsWithBooleanParam(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A group'],
            ['id' => 2, 'name' => 'Another group'],
        ];

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
    public function shouldGetAllGroupProjectsWithBooleanParam(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A group'],
            ['id' => 2, 'name' => 'Another group'],
        ];

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
    public function shouldNotNeedPaginationWhenGettingGroups(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A group'],
            ['id' => 2, 'name' => 'Another group'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups', [])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldShowGroup(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A group'];

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
    public function shouldCreateGroup(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A new group'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups', ['name' => 'A new group', 'path' => 'a-new-group', 'visibility' => 'private'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create('A new group', 'a-new-group'));
    }

    /**
     * @test
     */
    public function shouldCreateGroupWithDescriptionAndVisibility(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A new group', 'visibility_level' => 2];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups', ['name' => 'A new group', 'path' => 'a-new-group', 'description' => 'Description', 'visibility' => 'public'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create('A new group', 'a-new-group', 'Description', 'public'));
    }

    /**
     * @test
     */
    public function shouldCreateGroupWithDescriptionVisibilityAndParentId(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A new group', 'visibility_level' => 2, 'parent_id' => 666];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups', ['name' => 'A new group', 'path' => 'a-new-group', 'description' => 'Description', 'visibility' => 'public', 'parent_id' => 666])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create('A new group', 'a-new-group', 'Description', 'public', null, null, 666));
    }

    /**
     * @test
     */
    public function shouldUpdateGroup(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'Group name', 'path' => 'group-path'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('groups/3', ['name' => 'Group name', 'path' => 'group-path'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(3, ['name' => 'Group name', 'path' => 'group-path']));
    }

    /**
     * @test
     */
    public function shouldTransferProjectToGroup(): void
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
    public function shouldGetAllMembers(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'Bob'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/members/all')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->allMembers(1));
    }

    /**
     * @test
     */
    public function shouldGetMembers(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'Bob'],
        ];

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
    public function shouldAddMember(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Matt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups/1/members', ['user_id' => 2, 'access_level' => 3])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addMember(1, 2, 3));
    }

    /**
     * @test
     */
    public function shouldSaveMember(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Matt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('groups/1/members/2', ['access_level' => 4])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->saveMember(1, 2, 4));
    }

    /**
     * @test
     */
    public function shouldRemoveMember(): void
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
    public function shouldRemoveGroup(): void
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
    public function shouldGetAllSubgroups(): void
    {
        $expectedArray = [
            ['id' => 101, 'name' => 'A subgroup'],
            ['id' => 1 - 2, 'name' => 'Another subggroup'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/subgroups', ['page' => 1, 'per_page' => 10])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->subgroups(1, ['page' => 1, 'per_page' => 10]));
    }

    /**
     * @test
     */
    public function shouldGetLabels(): void
    {
        $expectedArray = [
            ['id' => 987, 'name' => 'bug', 'color' => '#000000'],
            ['id' => 123, 'name' => 'feature', 'color' => '#ff0000'],
        ];

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
    public function shouldAddLabel(): void
    {
        $expectedArray = ['name' => 'bug', 'color' => '#000000'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups/1/labels', ['name' => 'wont-fix', 'color' => '#ffffff'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addLabel(1, ['name' => 'wont-fix', 'color' => '#ffffff']));
    }

    /**
     * @test
     */
    public function shouldUpdateLabel(): void
    {
        $expectedArray = ['name' => 'bug', 'color' => '#00ffff'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('groups/1/labels/123', ['new_name' => 'big-bug', 'color' => '#00ffff'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateLabel(1, 123, ['new_name' => 'big-bug', 'color' => '#00ffff']));
    }

    /**
     * @test
     */
    public function shouldRemoveLabel(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('groups/1/labels/456', [])
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeLabel(1, 456));
    }

    public function shouldGetVariables(): void
    {
        $expectedArray = [
            ['key' => 'ftp_username', 'value' => 'ftp'],
            ['key' => 'ftp_password', 'value' => 'somepassword'],
        ];

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
    public function shouldGetVariable(): void
    {
        $expectedArray = ['key' => 'ftp_username', 'value' => 'ftp'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/variables/ftp_username')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->variable(1, 'ftp_username'));
    }

    public function shouldAddVariable(): void
    {
        $expectedKey = 'ftp_port';
        $expectedValue = '21';

        $expectedArray = [
            'key' => $expectedKey,
            'value' => $expectedValue,
        ];

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
    public function shouldAddVariableWithProtected(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'protected' => true,
        ];

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
    public function shouldUpdateVariable(): void
    {
        $expectedKey = 'ftp_port';
        $expectedValue = '22';

        $expectedArray = [
            'key' => 'ftp_port',
            'value' => '22',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('groups/1/variables/'.$expectedKey, ['value' => $expectedValue])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateVariable(1, $expectedKey, $expectedValue));
    }

    /**
     * @test
     */
    public function shouldUpdateVariableWithProtected(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'protected' => true,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('groups/1/variables/DEPLOY_SERVER', ['value' => 'stage.example.com', 'protected' => true])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true));
    }

    /**
     * @test
     */
    public function shouldRemoveVariable(): void
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
        return Groups::class;
    }

    /**
     * @test
     */
    public function shouldGetAllGroupProjectsWithIssuesEnabled(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A group', 'issues_enabled' => true],
            ['id' => 2, 'name' => 'Another group', 'issues_enabled' => true],
        ];

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
    public function shouldGetAllGroupProjectsWithMergeRequestsEnabled(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A group', 'merge_requests_enabled' => true],
            ['id' => 2, 'name' => 'Another group', 'merge_requests_enabled' => true],
        ];

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
    public function shouldGetAllGroupProjectsSharedToGroup(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A project', 'shared_with_groups' => [1]],
            ['id' => 2, 'name' => 'Another project', 'shared_with_groups' => [1]],
        ];

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
    public function shouldGetAllGroupProjectsIncludingSubsgroups(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A project'],
            ['id' => 2, 'name' => 'Another project', 'shared_with_groups' => [1]],
        ];

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
    public function shouldGetAllGroupProjectsIncludingCustomAttributes(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A project', 'custom_Attr' => true],
            ['id' => 2, 'name' => 'Another project', 'custom_Attr' => true],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/projects', ['with_custom_attributes' => 'true'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->projects(1, ['with_custom_attributes' => true]));
    }
}
