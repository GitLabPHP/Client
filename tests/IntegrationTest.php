<?php

declare(strict_types=1);

namespace Gitlab\Tests;

use Gitlab\Client;
use Gitlab\Exception\RuntimeException;
use Gitlab\ResultPager;
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

    public function testRepoBranches(): void
    {
        $client = new Client();

        $pager = new ResultPager($client);

        $branches = $pager->fetchAll(
            $client->repositories(),
            'branches',
            [5315609]
        );

        $this->assertIsArray($branches);
        $this->assertTrue(isset($response[0]));
        $this->assertTrue(isset($response[0]['name']));
    }
}
