<?php namespace Gitlab\Tests\Api;

use Gitlab\Api\AbstractApi;

class UsersTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllUsers()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'Matt'),
            array('id' => 2, 'name' => 'John'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users', array())
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldGetActiveUsers()
    {
        $expectedArray = array(
            array('id' => 1, 'name' => 'Matt'),
            array('id' => 2, 'name' => 'John'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users', array('active' => true))
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
        $expectedArray = array('id' => 1, 'name' => 'Matt');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('users/1')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1));
    }

    /**
     * @test
     */
    public function shouldCreateUser()
    {
        $expectedArray = array('id' => 3, 'name' => 'Billy');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users', array('email' => 'billy@example.com', 'password' => 'password'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create('billy@example.com', 'password'));
    }

    /**
     * @test
     */
    public function shouldCreateUserWithAdditionalInfo()
    {
        $expectedArray = array('id' => 3, 'name' => 'Billy');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users', array('email' => 'billy@example.com', 'password' => 'password', 'name' => 'Billy', 'bio' => 'A person'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create('billy@example.com', 'password', array('name' => 'Billy', 'bio' => 'A person')));
    }

    /**
     * @test
     */
    public function shouldUpdateUser()
    {
        $expectedArray = array('id' => 3, 'name' => 'Billy Bob');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('users/3', array('name' => 'Billy Bob'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(3, array('name' => 'Billy Bob')));
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
        $expectedArray = array('id' => 1, 'name' => 'Matt');

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
        $expectedArray = array(
            array('id' => 1, 'title' => 'A key'),
            array('id' => 2, 'name' => 'Another key'),
        );

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
        $expectedArray = array('id' => 1, 'title' => 'A key');

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
        $expectedArray = array('id' => 3, 'title' => 'A new key');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('user/keys', array('title' => 'A new key', 'key' => '...'))
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
        $expectedArray = array(
            array('id' => 1, 'title' => 'A key'),
            array('id' => 2, 'name' => 'Another key'),
        );

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
        $expectedArray = array('id' => 2, 'title' => 'Another key');

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
        $expectedArray = array('id' => 3, 'title' => 'A new key');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('users/1/keys', array('title' => 'A new key', 'key' => '...'))
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
        $expectedArray = array('id' => 1, 'name' => 'Matt');

        $api = $this->getApiMock();
        $api->expects($this->exactly(2))
            ->method('post')
            ->with('session', array('login' => 'matt', 'password' => 'password', 'email' => 'matt'))
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
        $expectedArray = array(
            array('id' => 1, 'email' => 'foo@bar.baz'),
            array('id' => 2, 'email' => 'foo@bar.qux'),
        );

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
        $expectedArray = array('id' => 1, 'email' => 'foo@bar.baz');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('user/emails/1')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->email(1));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\Users';
    }
}
