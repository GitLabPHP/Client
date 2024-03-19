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

use DateTime;
use Gitlab\Api\Integrations;

class IntegrationsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllIntegrations(): void
    {
        $expectedArray = $this->getMultipleIntegrationsData();
        $api = $this->getMultipleProjectsRequestMock('integrations', $expectedArray);

        $this->assertEquals($expectedArray, $api->all());
    }

    public function shouldCreateMicrosoftTeams(): void
    {
        $expectedArray = [
            'title' => 'Microsoft Teams notifications',
            'slug' => 'microsoft-teams'
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/integrations')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->createMicrosoftTeams(1, [
            'webroot' => 'http://test.org/',
        ]));

    }

    public function shouldUpdateMicrosoftTeams(): void
    {
        $expectedArray = [
            'title' => 'Microsoft Teams notifications',
            'slug' => 'microsoft-teams'
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/integrations')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->updateMicrosoftTeams(1, [
            'webroot' => 'http://test.org/',
        ]));
    }

    public function shouldGetMicrosoftTeams(): void
    {
        $expectedArray = [
            'title' => 'Microsoft Teams notifications',
            'slug' => 'microsoft-teams'
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/integrations')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->getMicrosoftTeams(1));
    }

    public function shouldRemoveMicrosoftTeams(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/integrations')
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->removeMicrosoftTeams(1));
    }



    public function shouldCreateJira(): void
    {
        $expectedArray = [
            'title' => 'Jira',
            'slug' => 'jira'
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/integrations')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->createJira(1, [
            'url' => 'http://test.org/',
            'password' => '123'
        ]));

    }

    public function shouldUpdateJira(): void
    {
        $expectedArray = [
            'title' => 'Jira',
            'slug' => 'jira'
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/integrations')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->updateJira(1, [
            'url' => 'http://test.org/',
            'password' => '123'
        ]));
    }

    public function shouldGetJira(): void
    {
        $expectedArray = [
            'title' => 'Jira',
            'slug' => 'jira'
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/integrations')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->getJira(1));
    }

    public function shouldRemoveJira(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/integrations')
            ->will($this->returnValue($expectedBool));

        $this->assertEquals($expectedBool, $api->removeJira(1));
    }



    protected function getMultipleIntegrationsData()
    {
        return [
            ['id' => 1, 'title' => 'Microsoft Teams notifications', 'slug' => 'microsoft-teams'],
            ['id' => 2, 'title' => 'Jira', 'slug' => 'jira']
        ];
    }

    protected function getMultipleProjectsRequestMock($path, $expectedArray = [], $expectedParameters = [])
    {
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with($path, $expectedParameters)
            ->will($this->returnValue($expectedArray));

        return $api;
    }

    protected function getApiClass()
    {
        return Projects::class;
    }

}
