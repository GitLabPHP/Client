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

use Gitlab\Api\AbstractApi;
use Gitlab\Client;
use Psr\Http\Client\ClientInterface;

class AbstractApiTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->api = $this->getTestApi();
    }

    /**
     * @test
     */
    public function shouldNotHaveTrailingSlashForEmptyUri(): void
    {
        $expectedString = 'projects/1';

        $this->assertEquals($expectedString,
            $this->api->getProjectPath($id = 1, $uri = '')
        );
    }

    /**
     * @test
     */
    public function shouldHaveTrailingSlashIfProvidedInUri(): void
    {
        $expectedString = 'projects/1/commits/';

        $this->assertEquals($expectedString,
            $this->api->getProjectPath($id = 1, $uri = 'commits/')
        );
    }

    protected function getTestApi(): TestApi
    {
        $httpClient = $this->getMockBuilder(ClientInterface::class)->getMock();
        $client = Client::createWithHttpClient($httpClient);

        return new TestApi($client);
    }

    protected function getApiClass(): void
    {
    }
}

class TestApi extends AbstractApi
{
    public function getProjectPath($id, string $uri): string
    {
        return parent::getProjectPath($id, $uri);
    }
}
