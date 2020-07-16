<?php

namespace Gitlab\Tests\Api;

use Gitlab\Api\Users;
class UsersTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllUsers()
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'John'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users', [])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldGetActiveUsers()
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'John'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users', ['active' => true])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(['active' => true]));
    }

    /**
     * @test
     */
    public function shouldGetUsersWithDateTimeParams()
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'John'],
        ];

        $createdAfter = new \DateTime('2018-01-01 00:00:00');
        $createdBefore = new \DateTime('2018-01-31 00:00:00');

        $expectedWithArray = [
            'created_after' => $createdAfter->format(DATE_ATOM),
            'created_before' => $createdBefore->format(DATE_ATOM),
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users', $expectedWithArray)
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals(
            $expectedArray,
            $api->all(['created_after' => $createdAfter, 'created_before' => $createdBefore])
        );
    }

    /**
     * @test
     */
    public function shouldShowUser()
    {
        $expectedArray = ['id' => 1, 'name' => 'Matt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1));
    }

    protected function getUsersProjectsData()
    {
        return [
            ['id' => 1, 'name' => 'matt-project-1'],
            ['id' => 2, 'name' => 'matt-project-2'],
        ];
    }

    protected function getUsersProjectsRequestMock($path, $expectedArray = [], $expectedParameters = [])
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
    public function shouldShowUsersProjects()
    {
        $expectedArray = $this->getUsersProjectsData();

        $api = $this->getUsersProjectsRequestMock('users/1/projects', $expectedArray);

        $this->assertEquals($expectedArray, $api->usersProjects(1));
    }

    /**
     * @test
     */
    public function shouldShowUsersProjectsWithLimit()
    {
        $expectedArray = [$this->getUsersProjectsData()[0]];

        $api = $this->getUsersProjectsRequestMock('users/1/projects', $expectedArray, ['per_page' => 1]);

        $this->assertEquals($expectedArray, $api->usersProjects(1, ['per_page' => 1]));
    }

    /**
     * @test
     */
    public function shouldGetAllUsersProjectsSortedByName()
    {
        $expectedArray = $this->getUsersProjectsData();

        $api = $this->getUsersProjectsRequestMock(
            'users/1/projects',
            $expectedArray,
            ['page' => 1, 'per_page' => 5, 'order_by' => 'name', 'sort' => 'asc']
        );

        $this->assertEquals(
            $expectedArray,
            $api->usersProjects(1, ['page' => 1, 'per_page' => 5, 'order_by' => 'name', 'sort' => 'asc'])
        );
    }

    /**
     * @test
     */
    public function shouldGetNotArchivedUsersProjects()
    {
        $expectedArray = $this->getUsersProjectsData();

        $api = $this->getUsersProjectsRequestMock('users/1/projects', $expectedArray, ['archived' => 'false']);

        $this->assertEquals($expectedArray, $api->usersProjects(1, ['archived' => false]));
    }

    /**
     * @test
     */
    public function shouldGetOwnedUsersProjects()
    {
        $expectedArray = $this->getUsersProjectsData();

        $api = $this->getUsersProjectsRequestMock('users/1/projects', $expectedArray, ['owned' => 'true']);

        $this->assertEquals($expectedArray, $api->usersProjects(1, ['owned' => true]));
    }

    public function possibleAccessLevels()
    {
        return [
            [10],
            [20],
            [30],
            [40],
            [50],
        ];
    }

    /**
     * @test
     * @dataProvider possibleAccessLevels
     */
    public function shouldGetProjectsWithMinimumAccessLevel($level)
    {
        $expectedArray = $this->getUsersProjectsData();

        $api = $this->getUsersProjectsRequestMock('users/1/projects', $expectedArray, ['min_access_level' => $level]);

        $this->assertEquals($expectedArray, $api->usersProjects(1, ['min_access_level' => $level]));
    }

    /**
     * @test
     */
    public function shouldSearchUsersProjects()
    {
        $expectedArray = $this->getUsersProjectsData();

        $api = $this->getUsersProjectsRequestMock('users/1/projects', $expectedArray, ['search' => 'a project']);
        $this->assertEquals($expectedArray, $api->usersProjects(1, ['search' => 'a project']));
    }

    /**
     * @test
     */
    public function shouldCreateUser()
    {
        $expectedArray = ['id' => 3, 'name' => 'Billy'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users', ['email' => 'billy@example.com', 'password' => 'password'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create('billy@example.com', 'password'));
    }

    /**
     * @test
     */
    public function shouldCreateUserWithAdditionalInfo()
    {
        $expectedArray = ['id' => 3, 'name' => 'Billy'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users', ['email' => 'billy@example.com', 'password' => 'password', 'name' => 'Billy', 'bio' => 'A person'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create('billy@example.com', 'password', ['name' => 'Billy', 'bio' => 'A person']));
    }

    /**
     * @test
     */
    public function shouldUpdateUser()
    {
        $expectedArray = ['id' => 3, 'name' => 'Billy Bob'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('users/3', ['name' => 'Billy Bob'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(3, ['name' => 'Billy Bob']));

        $expectedArray = ['id' => 4, 'avatar_url' => 'http://localhost:3000/uploads/user/avatar/4/image.jpg'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('users/4', [], [], ['avatar' => '/some/image.jpg'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(4, [], ['avatar' => '/some/image.jpg']));
    }

    /**
     * @test
     */
    public function shouldRemoveUser()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('users/1')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->remove(1));
    }

    /**
     * @test
     */
    public function shouldBlockUser()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users/1/block')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->block(1));
    }

    /**
     * @test
     */
    public function shouldUnblockUser()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users/1/unblock')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->unblock(1));
    }

    /**
     * @test
     */
    public function shouldShowCurrentUser()
    {
        $expectedArray = ['id' => 1, 'name' => 'Matt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('user')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->me());
    }

    /**
     * @test
     */
    public function shouldGetCurrentUserKeys()
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'A key'],
            ['id' => 2, 'name' => 'Another key'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('user/keys')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->keys(1));
    }

    /**
     * @test
     */
    public function shouldGetCurrentUserKey()
    {
        $expectedArray = ['id' => 1, 'title' => 'A key'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('user/keys/1')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->key(1));
    }

    /**
     * @test
     */
    public function shouldCreateKeyForCurrentUser()
    {
        $expectedArray = ['id' => 3, 'title' => 'A new key'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('user/keys', ['title' => 'A new key', 'key' => '...'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createKey('A new key', '...'));
    }

    /**
     * @test
     */
    public function shouldDeleteKeyForCurrentUser()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('user/keys/3')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeKey(3));
    }

    /**
     * @test
     */
    public function shouldGetUserKeys()
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'A key'],
            ['id' => 2, 'name' => 'Another key'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1/keys')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->userKeys(1));
    }

    /**
     * @test
     */
    public function shouldGetUserKey()
    {
        $expectedArray = ['id' => 2, 'title' => 'Another key'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1/keys/2')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->userKey(1, 2));
    }

    /**
     * @test
     */
    public function shouldCreateKeyForUser()
    {
        $expectedArray = ['id' => 3, 'title' => 'A new key'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users/1/keys', ['title' => 'A new key', 'key' => '...'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createKeyForUser(1, 'A new key', '...'));
    }

    /**
     * @test
     */
    public function shouldDeleteKeyForUser()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('users/1/keys/3')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeUserKey(1, 3));
    }

    /**
     * @test
     */
    public function shouldAttemptLogin()
    {
        $expectedArray = ['id' => 1, 'name' => 'Matt'];

        $api = $this->getApiMock();
        $api->expects($this->exactly(2))
            ->method('post')
            ->with('session', ['login' => 'matt', 'password' => 'password', 'email' => 'matt'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->session('matt', 'password'));
        $this->assertEquals($expectedArray, $api->login('matt', 'password'));
    }

    /**
     * @test
     */
    public function shouldGetUserEmails()
    {
        $expectedArray = [
            ['id' => 1, 'email' => 'foo@bar.baz'],
            ['id' => 2, 'email' => 'foo@bar.qux'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('user/emails')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->emails());
    }

    /**
     * @test
     */
    public function shouldGetSpecificUserEmail()
    {
        $expectedArray = ['id' => 1, 'email' => 'foo@bar.baz'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('user/emails/1')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->email(1));
    }

    /**
     * @test
     */
    public function shouldGetEmailsForUser()
    {
        $expectedArray = [
            ['id' => 1, 'email' => 'foo@bar.baz'],
            ['id' => 2, 'email' => 'foo@bar.qux'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1/emails')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->userEmails(1));
    }

    /**
     * @test
     */
    public function shouldCreateEmailForUser()
    {
        $expectedArray = ['id' => 3, 'email' => 'foo@bar.example'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users/1/emails', ['email' => 'foo@bar.example', 'skip_confirmation' => false])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createEmailForUser(1, 'foo@bar.example'));
    }

    /**
     * @test
     */
    public function shouldCreateConfirmedEmailForUser()
    {
        $expectedArray = ['id' => 4, 'email' => 'foo@baz.example'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users/1/emails', ['email' => 'foo@baz.example', 'skip_confirmation' => true])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createEmailForUser(1, 'foo@baz.example', true));
    }

    /**
     * @test
     */
    public function shouldDeleteEmailForUser()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('users/1/emails/3')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeUserEmail(1, 3));
    }

    /**
     * @test
     */
    public function shouldGetCurrentUserImpersonationTokens()
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A Name', 'revoked' => false],
            ['id' => 2, 'name' => 'A Name', 'revoked' => false],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1/impersonation_tokens')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->userImpersonationTokens(1));
    }

    /**
     * @test
     */
    public function shouldGetUserImpersonationToken()
    {
        $expectedArray = ['id' => 2, 'name' => 'name'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1/impersonation_tokens/1')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->userImpersonationToken(1, 1));
    }

    /**
     * @test
     */
    public function shouldCreateImpersonationTokenForUser()
    {
        $expectedArray = ['id' => 1, 'name' => 'name'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users/1/impersonation_tokens', ['name' => 'name', 'scopes' => ['api'], 'expires_at' => null])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createImpersonationToken(1, 'name', ['api']));
    }

    /**
     * @test
     */
    public function shouldDeleteImpersonationTokenForUser()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('users/1/impersonation_tokens/1')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeImpersonationToken(1, 1));
    }

    /**
     * @test
     */
    public function shouldGetCurrentUserActiveImpersonationTokens()
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A Name', 'revoked' => true],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1/impersonation_tokens')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->userImpersonationTokens(1, ['state' => 'active']));
    }

    /**
     * @test
     */
    public function shouldGetCurrentUserInactiveImpersonationTokens()
    {
        $expectedArray = [
            ['id' => 2, 'name' => 'A Name', 'revoked' => false],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1/impersonation_tokens')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->userImpersonationTokens(1, ['state' => 'inactive']));
    }

    protected function getApiClass()
    {
        return Users::class;
    }

    /**
     * @test
     */
    public function shouldGetEvents()
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An event'],
            ['id' => 2, 'title' => 'Another event'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1/events', [])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->events(1));
    }

    /**
     * @test
     */
    public function shouldGetEventsWithDateTimeParams()
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An event'],
            ['id' => 2, 'title' => 'Another event'],
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
            ->with('users/1/events', $expectedWithArray)
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->events(1, ['after' => $after, 'before' => $before]));
    }

    /**
     * @test
     */
    public function shouldGetEventsWithPagination()
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An event'],
            ['id' => 2, 'title' => 'Another event'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1/events', [
                'page' => 2,
                'per_page' => 15,
            ])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->events(1, ['page' => 2, 'per_page' => 15]));
    }
}
