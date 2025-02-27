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

use Gitlab\Api\Integrations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;

class IntegrationsTest extends TestCase
{
    #[Test]
    public function shouldGetAllIntegrations(): void
    {
        $expectedArray = $this->getMultipleIntegrationsData();
        $api = $this->getMultipleIntegrationsRequestMock('projects/1/integrations', $expectedArray);

        $this->assertEquals($expectedArray, $api->all(1));
    }

    #[Test]
    public function shouldCreateMicrosoftTeams(): void
    {
        $expectedArray = [
            'title' => 'Microsoft Teams notifications',
            'slug' => 'microsoft-teams',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/integrations/microsoft-teams')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->createMicrosoftTeams(1, [
            'webhook' => 'https://test.org/',
        ]));
    }

    #[Test]
    public function shouldUpdateMicrosoftTeams(): void
    {
        $expectedArray = [
            'title' => 'Microsoft Teams notifications',
            'slug' => 'microsoft-teams',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/integrations/microsoft-teams')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->updateMicrosoftTeams(1, [
            'webhook' => 'https://test.org/',
        ]));
    }

    #[Test]
    public function shouldGetMicrosoftTeams(): void
    {
        $expectedArray = [
            'title' => 'Microsoft Teams notifications',
            'slug' => 'microsoft-teams',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/integrations/microsoft-teams')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->getMicrosoftTeams(1));
    }

    #[Test]
    public function shouldRemoveMicrosoftTeams(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/integrations/microsoft-teams')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeMicrosoftTeams(1));
    }

    #[Test]
    public function shouldCreateJira(): void
    {
        $expectedArray = [
            'title' => 'Jira',
            'slug' => 'jira',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/integrations/jira')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->createJira(1, [
            'url' => 'https://test.org/',
            'password' => '123',
        ]));
    }

    #[Test]
    public function shouldUpdateJira(): void
    {
        $expectedArray = [
            'title' => 'Jira',
            'slug' => 'jira',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/integrations/jira')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->updateJira(1, [
            'url' => 'https://test.org/',
            'password' => '123',
        ]));
    }

    #[Test]
    public function shouldGetJira(): void
    {
        $expectedArray = [
            'title' => 'Jira',
            'slug' => 'jira',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/integrations/jira')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->getJira(1));
    }

    #[Test]
    public function shouldRemoveJira(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/integrations/jira')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeJira(1));
    }

    // This method is used to create an array of multiple integrations data.
    // NOT A TEST
    private function getMultipleIntegrationsData(): array
    {
        return [
            ['id' => 1, 'title' => 'Microsoft Teams notifications', 'slug' => 'microsoft-teams'],
            ['id' => 2, 'title' => 'Jira', 'slug' => 'jira'],
        ];
    }

    // This method is used to create a mock for the Integrations class.
    // NOT A TEST
    private function getMultipleIntegrationsRequestMock($path, $expectedArray = [], $expectedParameters = []): MockObject
    {
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with($path, $expectedParameters)
            ->willReturn($expectedArray);

        return $api;
    }

    protected function getApiClass(): string
    {
        return Integrations::class;
    }
}
