<?php namespace Gitlab\Tests\Api;

use Gitlab\Api\AbstractApi;

class GroupsTest extends ApiTestCase
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

        $this->assertEquals($expectedArray, $api->all(1, 10));
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
            ->with('groups', array('page' => 1, 'per_page' => AbstractApi::PER_PAGE))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldSearchGroups()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'A group'),
            array('id' => 2, 'name' => 'Another group'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups?search=some%20group', array('page' => 1, 'per_page' => AbstractApi::PER_PAGE))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->search('some group'));
    }

    /**
     * @test
     */
    public function shouldSearchGroupsWithPagination()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'A group'),
            array('id' => 2, 'name' => 'Another group'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups?search=group', array('page' => 2, 'per_page' => 5))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->search('group', 2, 5));
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
            ->with('groups', array('name' => 'A new group', 'path' => 'a-new-group', 'description' => null, 'visibility_level' => 0))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create('A new group', 'a-new-group'));
    }

    /**
     * @test
     */
    public function shouldCreateGroupWithDescriptionAndVisLevel()
    {
        $expectedArray = array('id' => 1, 'name' => 'A new group', 'visibility_level' => 2);

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('groups', array('name' => 'A new group', 'path' => 'a-new-group', 'description' => 'Description', 'visibility_level' => 2))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create('A new group', 'a-new-group', 'Description', 2));
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

    protected function getApiClass()
    {
        return 'Gitlab\Api\Groups';
    }
}
