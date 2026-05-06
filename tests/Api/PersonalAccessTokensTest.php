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
use PHPUnit\Framework\Attributes\Test;

class PersonalAccessTokensTest extends TestCase
{
    #[Test]
    public function shouldGetAllTokens(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Token 1', 'state' => 'active'],
            ['id' => 2, 'name' => 'Token 2', 'state' => 'inactive'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('personal_access_tokens', [])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    #[Test]
    public function shouldGetAllTokensWithFilters(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Token 1', 'state' => 'active'],
        ];
        $createdAfter = new \DateTimeImmutable('2025-01-01 00:00:00');
        $createdBefore = new \DateTimeImmutable('2025-02-01 00:00:00');
        $expiresAfter = new \DateTimeImmutable('2025-03-01 00:00:00');
        $expiresBefore = new \DateTimeImmutable('2025-04-01 00:00:00');
        $lastUsedAfter = new \DateTimeImmutable('2025-05-01 00:00:00');
        $lastUsedBefore = new \DateTimeImmutable('2025-06-01 00:00:00');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('personal_access_tokens', [
                'search' => 'Token',
                'state' => 'active',
                'user_id' => 1,
                'revoked' => 'false',
                'created_after' => $createdAfter->format('c'),
                'created_before' => $createdBefore->format('c'),
                'expires_after' => $expiresAfter->format('Y-m-d'),
                'expires_before' => $expiresBefore->format('Y-m-d'),
                'last_used_after' => $lastUsedAfter->format('c'),
                'last_used_before' => $lastUsedBefore->format('c'),
                'sort' => 'name_desc',
            ])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all([
            'search' => 'Token',
            'state' => 'active',
            'user_id' => 1,
            'revoked' => false,
            'created_after' => $createdAfter,
            'created_before' => $createdBefore,
            'expires_after' => $expiresAfter,
            'expires_before' => $expiresBefore,
            'last_used_after' => $lastUsedAfter,
            'last_used_before' => $lastUsedBefore,
            'sort' => 'name_desc',
        ]));
    }

    #[Test]
    public function shouldShowToken(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Token 1', 'state' => 'active'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('personal_access_tokens/1')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->show(1));
    }

    #[Test]
    public function shouldShowCurrentToken(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Token 1', 'state' => 'active'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('personal_access_tokens/self')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->current());
    }

    #[Test]
    public function shouldRotateToken(): void
    {
        $expectedArray = ['id' => 4, 'name' => 'Token 4'];
        $expiresAt = new \DateTimeImmutable('2025-07-01 00:00:00');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('personal_access_tokens/3/rotate', ['expires_at' => $expiresAt->format('Y-m-d')])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->rotate(3, ['expires_at' => $expiresAt]));
    }

    #[Test]
    public function shouldRotateCurrentToken(): void
    {
        $expectedArray = ['id' => 4, 'name' => 'Token 4'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('personal_access_tokens/self/rotate', [])
            ->willReturn($expectedArray)
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
            ->willReturn($expectedBool)
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
            ->willReturn($expectedBool)
        ;

        $this->assertEquals($expectedBool, $api->removeCurrent());
    }

    protected function getApiClass(): string
    {
        return PersonalAccessTokens::class;
    }
}
