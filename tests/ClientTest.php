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
use GuzzleHttp\Psr7\Response;
use Http\Client\Common\HttpMethodsClient;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

class ClientTest extends TestCase
{
    public function testCreateClient(): void
    {
        $client = new Client();

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(HttpMethodsClient::class, $client->getHttpClient());
    }

    public static function authenticationProvider(): array
    {
        return [
            Client::AUTH_HTTP_TOKEN => [Client::AUTH_HTTP_TOKEN, 'PRIVATE-TOKEN', 'my-token'],
            Client::AUTH_HTTP_JOB_TOKEN => [Client::AUTH_HTTP_JOB_TOKEN, 'JOB-TOKEN', 'my-token'],
            Client::AUTH_OAUTH_TOKEN => [Client::AUTH_OAUTH_TOKEN, 'Authorization', 'Bearer my-token'],
        ];
    }

    #[DataProvider('authenticationProvider')]
    public function testAuthenticationIsSentToTheBaseUrl(string $method, string $header, string $value): void
    {
        $requests = [];

        $client = Client::createWithHttpClient($this->createHttpClient($requests));
        $client->authenticate('my-token', $method, 'my-user');
        $client->getHttpClient()->get('/api/v4/user');
        $client->getHttpClient()->get('https://GitLab.com:443/api/v4/projects?page=2');

        $this->assertCount(2, $requests);

        foreach ($requests as $request) {
            $this->assertSame($value, $request->getHeaderLine($header));
            $this->assertSame('my-user', $request->getHeaderLine('SUDO'));
        }
    }

    public function testAuthenticationIsNotSentToOtherOrigins(): void
    {
        $requests = [];

        $client = Client::createWithHttpClient($this->createHttpClient($requests));
        $client->authenticate('my-token', Client::AUTH_HTTP_TOKEN, 'my-user');
        $client->getHttpClient()->get('https://about.gitlab.com/api/v4/user');
        $client->getHttpClient()->get('http://gitlab.com/api/v4/user');
        $client->getHttpClient()->get('https://gitlab.com:8443/api/v4/user');
        $client->getHttpClient()->get('https://gitlab.com.example.com/api/v4/user');

        $this->assertCount(4, $requests);

        foreach ($requests as $request) {
            $this->assertFalse($request->hasHeader('PRIVATE-TOKEN'), (string) $request->getUri());
            $this->assertFalse($request->hasHeader('SUDO'), (string) $request->getUri());
        }
    }

    #[DataProvider('authenticationProvider')]
    public function testAuthenticationIsRemovedFromRequestsToOtherOrigins(string $method, string $header, string $value): void
    {
        $requests = [];

        $client = Client::createWithHttpClient($this->createHttpClient($requests));
        $client->authenticate('my-token', $method, 'my-user');
        $client->getHttpClient()->get('https://gitlab.example.com/api/v4/user', [$header => $value, 'SUDO' => 'my-user']);
        $client->getHttpClient()->get('https://gitlab.example.com/api/v4/user', [$header => 'other-token', 'SUDO' => 'my-user']);

        $this->assertFalse($requests[0]->hasHeader($header));
        $this->assertFalse($requests[0]->hasHeader('SUDO'));
        $this->assertSame('other-token', $requests[1]->getHeaderLine($header));
        $this->assertSame('my-user', $requests[1]->getHeaderLine('SUDO'));
    }

    public function testAuthenticationFollowsTheBaseUrl(): void
    {
        $requests = [];

        $client = Client::createWithHttpClient($this->createHttpClient($requests));
        $client->authenticate('my-token', Client::AUTH_HTTP_TOKEN);
        $client->setUrl('https://gitlab.example.com');
        $client->getHttpClient()->get('/api/v4/user');
        $client->getHttpClient()->get('https://gitlab.com/api/v4/user');

        $this->assertSame('gitlab.example.com', $requests[0]->getUri()->getHost());
        $this->assertSame('my-token', $requests[0]->getHeaderLine('PRIVATE-TOKEN'));
        $this->assertFalse($requests[1]->hasHeader('PRIVATE-TOKEN'));
    }

    public function testAuthenticationIsSentToTheBaseUrlOnRedirect(): void
    {
        $requests = [];

        $client = Client::createWithHttpClient($this->createHttpClient($requests, new Response(302, ['Location' => 'https://gitlab.com/api/v4/projects/2'])));
        $client->authenticate('my-token', Client::AUTH_HTTP_TOKEN);
        $client->getHttpClient()->get('/api/v4/projects/1');

        $this->assertSame('/api/v4/projects/2', $requests[1]->getUri()->getPath());
        $this->assertSame('my-token', $requests[1]->getHeaderLine('PRIVATE-TOKEN'));
    }

    #[DataProvider('authenticationProvider')]
    public function testAuthenticationIsNotSentToOtherOriginsOnRedirect(string $method, string $header, string $value): void
    {
        $requests = [];

        $client = Client::createWithHttpClient($this->createHttpClient($requests, new Response(302, ['Location' => 'https://storage.example.com/artifacts.zip?Signature=abc'])));
        $client->authenticate('my-token', $method, 'my-user');
        $client->jobs()->artifacts(1, 2);

        $this->assertSame($value, $requests[0]->getHeaderLine($header));
        $this->assertSame('my-user', $requests[0]->getHeaderLine('SUDO'));
        $this->assertSame('storage.example.com', $requests[1]->getUri()->getHost());
        $this->assertFalse($requests[1]->hasHeader($header));
        $this->assertFalse($requests[1]->hasHeader('SUDO'));
    }

    /**
     * @param RequestInterface[] $requests
     */
    private function createHttpClient(array &$requests, Response ...$responses): ClientInterface
    {
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient
            ->expects($this->any())
            ->method('sendRequest')
            ->willReturnCallback(static function (RequestInterface $request) use (&$requests, &$responses): Response {
                $requests[] = $request;

                return \array_shift($responses) ?? new Response();
            });

        return $httpClient;
    }
}
