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

use Gitlab\Api\PersonalAccessTokens;

class PersonalAccessTokensTest extends TestCase
{
    protected function getApiClass(): string
    {
        return PersonalAccessTokens::class;
    }

    #[Test]
    public function shouldGetAllTokens(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Token 1', 'state' => 'active'],
            ['id' => 2, 'name' => 'Token 2', 'state' => 'revoked'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('personal_access_tokens', [])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    #[Test]
    public function shouldGetActiveTokens(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Token 1', 'state' => 'active'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('personal_access_tokens', ['state' => 'active'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(['state' => 'active']));
    }

    #[Test]
    public function shouldShowToken(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Token 1', 'state' => 'active'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('personal_access_tokens/1')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1));
    }

    #[Test]
    public function shouldShowCurrent(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Token 1', 'state' => 'active'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('personal_access_tokens/self')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->current());
    }

    #[Test]
    public function shouldRotate(): void
    {
        $expectedArray = ['id' => 4, 'name' => 'Token 4'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('personal_access_tokens/3/rotate')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->rotate(3));
    }

    #[Test]
    public function shouldRotateCurrent(): void
    {
        $expectedArray = ['id' => 4, 'name' => 'Token 4'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('personal_access_tokens/self/rotate')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->rotateCurrent());
    }

    #[Test]
    public function shouldRemoveToken(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('personal_access_tokens/1')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->remove(1));
    }

    #[Test]
    public function shouldRemoveCurrentToken(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('personal_access_tokens/self')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeCurrent());
    }
}
