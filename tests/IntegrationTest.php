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

namespace Gitlab\Tests;

use Gitlab\Client;
use Gitlab\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase
{
    public function testRepoContributors(): void
    {
        $client = new Client();

        $response = $client
            ->repositories()
            ->contributors(5315609);

        $this->assertIsArray($response);
        $this->assertTrue(isset($response[2]));
        $this->assertTrue(isset($response[2]['name']));
    }

    public function testRepoNotFound(): void
    {
        $client = new Client();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('404 Project Not Found');

        $client
            ->repositories()
            ->contributors(1);
    }
}
