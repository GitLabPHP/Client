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

use Gitlab\Api\SystemHooks;
use PHPUnit\Framework\Attributes\Test;

class SystemHooksTest extends TestCase
{
    #[Test]
    public function shouldGetAllHooks(): void
    {
        $expectedArray = [
            ['id' => 1, 'url' => 'http://www.example.com'],
            ['id' => 2, 'url' => 'http://www.example.org'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('hooks')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all());
    }

    #[Test]
    public function shouldCreateHook(): void
    {
        $expectedArray = ['id' => 3, 'url' => 'http://www.example.net'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('hooks', ['url' => 'http://www.example.net'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->create('http://www.example.net'));
    }

    #[Test]
    public function shouldTestHook(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('hooks/3')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->test(3));
    }

    #[Test]
    public function shouldRemoveHook(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('hooks/3')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->remove(3));
    }

    protected function getApiClass(): string
    {
        return SystemHooks::class;
    }
}
