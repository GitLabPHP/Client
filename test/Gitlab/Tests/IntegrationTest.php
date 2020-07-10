<?php

namespace Gitlab\Tests;

use Gitlab\Client;
use Gitlab\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase
{
    public function testRepoContributors()
    {
        $client = new Client();

        $response = $client
            ->repositories()
            ->contributors(5315609);

        $this->assertInternalType('array', $response);
        $this->assertTrue(isset($response[2]));
        $this->assertTrue(isset($response[2]['name']));
    }

    public function testRepoNotFound()
    {
        $client = new Client();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('404 Project Not Found');

        $client
            ->repositories()
            ->contributors(1);
    }
}
