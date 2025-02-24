<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Tests\Api;

use Gitlab\Api\Users;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;

class UsersTest extends TestCase
{
    #[Test]
    public function shouldGetAllUsers(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'John'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users', [])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    #[Test]
    public function shouldGetActiveUsers(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'John'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users', ['active' => true])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(['active' => true]));
    }

    #[Test]
    public function shouldGetUsersWithDateTimeParams(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'John'],
        ];

        $createdAfter = new \DateTime('2018-01-01 00:00:00');
        $createdBefore = new \DateTime('2018-01-31 00:00:00');

        $expectedWithArray = [
            'created_after' => $createdAfter->format(\DATE_ATOM),
            'created_before' => $createdBefore->format(\DATE_ATOM),
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users', $expectedWithArray)
            ->willReturn($expectedArray)
        ;

        $this->assertEquals(
            $expectedArray,
            $api->all(['created_after' => $createdAfter, 'created_before' => $createdBefore])
        );
    }

    #[Test]
    public function shouldShowUser(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Matt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->show(1));
    }

    protected function getUsersMembershipsData(): array
    {
        return [
            [
                'source_id' => 1,
                'source_name' => 'Project one',
                'source_type' => 'Project',
                'access_level' => '20',
            ],
            [
                'source_id' => 3,
                'source_name' => 'Group three',
                'source_type' => 'Namespace',
                'access_level' => '20',
            ],
        ];
    }

    protected function getUsersMembershipsRequestMock($path, $expectedArray = [], $expectedParameters = []): MockObject
    {
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with($path, $expectedParameters)
            ->willReturn($expectedArray)
        ;

        return $api;
    }

    #[Test]
    public function shouldShowUsersMemberships(): void
    {
        $expectedArray = $this->getUsersMembershipsData();

        $api = $this->getUsersMembershipsRequestMock('users/1/memberships', $expectedArray);

        $this->assertEquals($expectedArray, $api->usersMemberships(1));
    }

    #[Test]
    public function shouldShowUsersMembershipsWithTypeProject(): void
    {
        $expectedArray = [$this->getUsersMembershipsData()[0]];

        $api = $this->getUsersMembershipsRequestMock('users/1/memberships', $expectedArray, ['type' => 'Project']);

        $this->assertEquals($expectedArray, $api->usersMemberships(1, ['type' => 'Project']));
    }

    #[Test]
    public function shouldShowUsersMembershipsWithTypeNamespace(): void
    {
        $expectedArray = [$this->getUsersMembershipsData()[1]];

        $api = $this->getUsersMembershipsRequestMock('users/1/memberships', $expectedArray, ['type' => 'Namespace']);

        $this->assertEquals($expectedArray, $api->usersMemberships(1, ['type' => 'Namespace']));
    }

    protected function getUsersProjectsData(): array
    {
        return [
            ['id' => 1, 'name' => 'matt-project-1'],
            ['id' => 2, 'name' => 'matt-project-2'],
        ];
    }

    protected function getUsersProjectsRequestMock($path, $expectedArray = [], $expectedParameters = []): MockObject
    {
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with($path, $expectedParameters)
            ->willReturn($expectedArray)
        ;

        return $api;
    }

    #[Test]
    public function shouldShowUsersProjects(): void
    {
        $expectedArray = $this->getUsersProjectsData();

        $api = $this->getUsersProjectsRequestMock('users/1/projects', $expectedArray);

        $this->assertEquals($expectedArray, $api->usersProjects(1));
    }

    #[Test]
    public function shouldShowUsersProjectsWithLimit(): void
    {
        $expectedArray = [$this->getUsersProjectsData()[0]];

        $api = $this->getUsersProjectsRequestMock('users/1/projects', $expectedArray, ['per_page' => 1]);

        $this->assertEquals($expectedArray, $api->usersProjects(1, ['per_page' => 1]));
    }

    #[Test]
    public function shouldGetAllUsersProjectsSortedByName(): void
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

    #[Test]
    public function shouldGetNotArchivedUsersProjects(): void
    {
        $expectedArray = $this->getUsersProjectsData();

        $api = $this->getUsersProjectsRequestMock('users/1/projects', $expectedArray, ['archived' => 'false']);

        $this->assertEquals($expectedArray, $api->usersProjects(1, ['archived' => false]));
    }

    #[Test]
    public function shouldGetOwnedUsersProjects(): void
    {
        $expectedArray = $this->getUsersProjectsData();

        $api = $this->getUsersProjectsRequestMock('users/1/projects', $expectedArray, ['owned' => 'true']);

        $this->assertEquals($expectedArray, $api->usersProjects(1, ['owned' => true]));
    }

    public static function possibleAccessLevels(): array
    {
        return [
            [10],
            [20],
            [30],
            [40],
            [50],
        ];
    }

    #[Test]
    #[DataProvider('possibleAccessLevels')]
    public function shouldGetProjectsWithMinimumAccessLevel($level): void
    {
        $expectedArray = $this->getUsersProjectsData();

        $api = $this->getUsersProjectsRequestMock('users/1/projects', $expectedArray, ['min_access_level' => $level]);

        $this->assertEquals($expectedArray, $api->usersProjects(1, ['min_access_level' => $level]));
    }

    #[Test]
    public function shouldSearchUsersProjects(): void
    {
        $expectedArray = $this->getUsersProjectsData();

        $api = $this->getUsersProjectsRequestMock('users/1/projects', $expectedArray, ['search' => 'a project']);
        $this->assertEquals($expectedArray, $api->usersProjects(1, ['search' => 'a project']));
    }

    #[Test]
    public function shouldShowUsersStarredProjects(): void
    {
        $expectedArray = $this->getUsersProjectsData();

        $api = $this->getUsersProjectsRequestMock('users/1/starred_projects', $expectedArray);

        $this->assertEquals($expectedArray, $api->usersStarredProjects(1));
    }

    #[Test]
    public function shouldShowUsersStarredProjectsWithLimit(): void
    {
        $expectedArray = [$this->getUsersProjectsData()[0]];

        $api = $this->getUsersProjectsRequestMock('users/1/starred_projects', $expectedArray, ['per_page' => 1]);

        $this->assertEquals($expectedArray, $api->usersStarredProjects(1, ['per_page' => 1]));
    }

    #[Test]
    public function shouldGetAllUsersStarredProjectsSortedByName(): void
    {
        $expectedArray = $this->getUsersProjectsData();

        $api = $this->getUsersProjectsRequestMock(
            'users/1/starred_projects',
            $expectedArray,
            ['page' => 1, 'per_page' => 5, 'order_by' => 'name', 'sort' => 'asc']
        );

        $this->assertEquals(
            $expectedArray,
            $api->usersStarredProjects(1, ['page' => 1, 'per_page' => 5, 'order_by' => 'name', 'sort' => 'asc'])
        );
    }

    #[Test]
    public function shouldGetNotArchivedUsersStarredProjects(): void
    {
        $expectedArray = $this->getUsersProjectsData();

        $api = $this->getUsersProjectsRequestMock('users/1/starred_projects', $expectedArray, ['archived' => 'false']);

        $this->assertEquals($expectedArray, $api->usersStarredProjects(1, ['archived' => false]));
    }

    #[Test]
    public function shouldGetOwnedUsersStarredProjects(): void
    {
        $expectedArray = $this->getUsersProjectsData();

        $api = $this->getUsersProjectsRequestMock('users/1/starred_projects', $expectedArray, ['owned' => 'true']);

        $this->assertEquals($expectedArray, $api->usersStarredProjects(1, ['owned' => true]));
    }

    #[Test]
    #[DataProvider('possibleAccessLevels')]
    public function shouldGetStarredProjectsWithMinimumAccessLevel($level): void
    {
        $expectedArray = $this->getUsersProjectsData();

        $api = $this->getUsersProjectsRequestMock('users/1/starred_projects', $expectedArray, ['min_access_level' => $level]);

        $this->assertEquals($expectedArray, $api->usersStarredProjects(1, ['min_access_level' => $level]));
    }

    #[Test]
    public function shouldSearchUsersStarredProjects(): void
    {
        $expectedArray = $this->getUsersProjectsData();

        $api = $this->getUsersProjectsRequestMock('users/1/starred_projects', $expectedArray, ['search' => 'a project']);
        $this->assertEquals($expectedArray, $api->usersStarredProjects(1, ['search' => 'a project']));
    }

    #[Test]
    public function shouldCreateUser(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'Billy'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users', ['email' => 'billy@example.com', 'password' => 'password'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->create('billy@example.com', 'password'));
    }

    #[Test]
    public function shouldCreateUserWithAdditionalInfo(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'Billy'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users', ['email' => 'billy@example.com', 'password' => 'password', 'name' => 'Billy', 'bio' => 'A person'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->create('billy@example.com', 'password', ['name' => 'Billy', 'bio' => 'A person']));
    }

    #[Test]
    public function shouldUpdateUser(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'Billy Bob'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('users/3', ['name' => 'Billy Bob'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->update(3, ['name' => 'Billy Bob']));

        $expectedArray = ['id' => 4, 'avatar_url' => 'http://localhost:3000/uploads/user/avatar/4/image.jpg'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('users/4', [], [], ['avatar' => '/some/image.jpg'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->update(4, [], ['avatar' => '/some/image.jpg']));
    }

    #[Test]
    public function shouldRemoveUser(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('users/1')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->remove(1));
    }

    #[Test]
    public function shouldBlockUser(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users/1/block')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->block(1));
    }

    #[Test]
    public function shouldUnblockUser(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users/1/unblock')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->unblock(1));
    }

    #[Test]
    public function shouldActivateUser(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users/1/activate')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->activate(1));
    }

    #[Test]
    public function shouldDeactivateUser(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users/1/deactivate')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->deactivate(1));
    }

    #[Test]
    public function shouldShowCurrentUser(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Matt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('user')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->me());
    }

    #[Test]
    public function shouldGetCurrentUserKeys(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'A key'],
            ['id' => 2, 'name' => 'Another key'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('user/keys')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->keys(1));
    }

    #[Test]
    public function shouldGetCurrentUserKey(): void
    {
        $expectedArray = ['id' => 1, 'title' => 'A key'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('user/keys/1')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->key(1));
    }

    #[Test]
    public function shouldCreateKeyForCurrentUser(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'A new key'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('user/keys', ['title' => 'A new key', 'key' => '...'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->createKey('A new key', '...'));
    }

    #[Test]
    public function shouldDeleteKeyForCurrentUser(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('user/keys/3')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeKey(3));
    }

    #[Test]
    public function shouldGetUserKeys(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'A key'],
            ['id' => 2, 'name' => 'Another key'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1/keys')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->userKeys(1));
    }

    #[Test]
    public function shouldGetUserKey(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'Another key'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1/keys/2')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->userKey(1, 2));
    }

    #[Test]
    public function shouldCreateKeyForUser(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'A new key'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users/1/keys', ['title' => 'A new key', 'key' => '...'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->createKeyForUser(1, 'A new key', '...'));
    }

    #[Test]
    public function shouldDeleteKeyForUser(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('users/1/keys/3')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeUserKey(1, 3));
    }

    #[Test]
    public function shouldGetUserEmails(): void
    {
        $expectedArray = [
            ['id' => 1, 'email' => 'foo@bar.baz'],
            ['id' => 2, 'email' => 'foo@bar.qux'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('user/emails')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->emails());
    }

    #[Test]
    public function shouldGetSpecificUserEmail(): void
    {
        $expectedArray = ['id' => 1, 'email' => 'foo@bar.baz'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('user/emails/1')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->email(1));
    }

    #[Test]
    public function shouldGetEmailsForUser(): void
    {
        $expectedArray = [
            ['id' => 1, 'email' => 'foo@bar.baz'],
            ['id' => 2, 'email' => 'foo@bar.qux'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1/emails')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->userEmails(1));
    }

    #[Test]
    public function shouldCreateEmailForUser(): void
    {
        $expectedArray = ['id' => 3, 'email' => 'foo@bar.example'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users/1/emails', ['email' => 'foo@bar.example', 'skip_confirmation' => false])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->createEmailForUser(1, 'foo@bar.example'));
    }

    #[Test]
    public function shouldCreateConfirmedEmailForUser(): void
    {
        $expectedArray = ['id' => 4, 'email' => 'foo@baz.example'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users/1/emails', ['email' => 'foo@baz.example', 'skip_confirmation' => true])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->createEmailForUser(1, 'foo@baz.example', true));
    }

    #[Test]
    public function shouldDeleteEmailForUser(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('users/1/emails/3')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeUserEmail(1, 3));
    }

    #[Test]
    public function shouldGetCurrentUserImpersonationTokens(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A Name', 'revoked' => false],
            ['id' => 2, 'name' => 'A Name', 'revoked' => false],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1/impersonation_tokens')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->userImpersonationTokens(1));
    }

    #[Test]
    public function shouldGetUserImpersonationToken(): void
    {
        $expectedArray = ['id' => 2, 'name' => 'name'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1/impersonation_tokens/1')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->userImpersonationToken(1, 1));
    }

    #[Test]
    public function shouldCreateImpersonationTokenForUser(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'name'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users/1/impersonation_tokens', ['name' => 'name', 'scopes' => ['api'], 'expires_at' => null])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->createImpersonationToken(1, 'name', ['api']));
    }

    #[Test]
    public function shouldDeleteImpersonationTokenForUser(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('users/1/impersonation_tokens/1')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeImpersonationToken(1, 1));
    }

    #[Test]
    public function shouldGetCurrentUserActiveImpersonationTokens(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A Name', 'revoked' => true],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1/impersonation_tokens')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->userImpersonationTokens(1, ['state' => 'active']));
    }

    #[Test]
    public function shouldGetCurrentUserInactiveImpersonationTokens(): void
    {
        $expectedArray = [
            ['id' => 2, 'name' => 'A Name', 'revoked' => false],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1/impersonation_tokens')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->userImpersonationTokens(1, ['state' => 'inactive']));
    }

    protected function getApiClass(): string
    {
        return Users::class;
    }

    #[Test]
    public function shouldGetEvents(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An event'],
            ['id' => 2, 'title' => 'Another event'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1/events', [])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->events(1));
    }

    #[Test]
    public function shouldGetEventsWithDateTimeParams(): void
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->events(1, ['after' => $after, 'before' => $before]));
    }

    #[Test]
    public function shouldGetEventsWithPagination(): void
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
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->events(1, ['page' => 2, 'per_page' => 15]));
    }

    #[Test]
    public function getRemoveUserIdentity(): void
    {
        $expectedArray = [
            ['id' => 1, 'identities' => []],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('users/1/identities/test')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->removeUserIdentity(1, 'test'));
    }
}
