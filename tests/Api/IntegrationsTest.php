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

class IntegrationsTest extends TestCase
{
    #[Test]
    public function shouldGetAllIntegrations(): void
    {
        $expectedArray = [
            ['id' => 75, 'title' => 'Jenkins CI', 'slug' => 'jenkins'],
            ['id' => 76, 'title' => 'Alerts endpoint', 'slug' => 'alerts'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/integrations')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(1));
    }

    #[Test]
    public function shouldShowIntegration(): void
    {
        $expectedArray = [
            'id' => 1,
            'title' => 'Jira',
            'slug' => 'jira',
            'active' => true,
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/integrations/jira')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->show(1, 'jira'));
    }

    #[Test]
    public function shouldSetIntegration(): void
    {
        $expectedArray = [
            'id' => 2,
            'title' => 'Microsoft Teams notifications',
            'slug' => 'microsoft-teams',
            'active' => true,
        ];
        $parameters = [
            'webhook' => 'https://example.com/webhook',
            'push_events' => true,
            'branches_to_be_notified' => 'default',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/integrations/microsoft-teams', $parameters)
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->set(1, 'microsoft-teams', $parameters));
    }

    #[Test]
    public function shouldRemoveIntegration(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/integrations/microsoft-teams')
            ->willReturn($expectedBool)
        ;

        $this->assertEquals($expectedBool, $api->remove(1, 'microsoft-teams'));
    }

    #[Test]
    public function shouldEncodeProjectPathAndIntegrationSlug(): void
    {
        $expectedArray = ['slug' => 'custom/integration'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/group%2Fproject/integrations/custom%2Fintegration')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->show('group/project', 'custom/integration'));
    }

    protected function getApiClass(): string
    {
        return Integrations::class;
    }
}
