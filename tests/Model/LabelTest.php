<?php

declare(strict_types=1);

namespace Gitlab\Tests\Model;

use Gitlab\Client;
use Gitlab\Model\Label;
use Gitlab\Model\Project;
use PHPUnit\Framework\TestCase;

class LabelTest extends TestCase
{
    public function testCorrectConstructWithoutClient(): void
    {
        $project = new Project();

        $sUT = new Label($project);

        $this->assertSame(null, $sUT->getClient());
    }

    public function testCorrectConstruct(): void
    {
        $project = new Project();
        $client = $this->createMock(Client::class);

        $sUT = new Label($project, $client);

        $this->assertSame($client, $sUT->getClient());
    }

    public function testFromArray(): void
    {
        $project = new Project();
        $client = $this->createMock(Client::class);

        $sUT = Label::fromArray($client, $project, ['color' => '#FF0000', 'name' => 'Testing', 'id' => 123]);

        $this->assertSame('#FF0000', $sUT->color);
        $this->assertSame('Testing', $sUT->name);
        $this->assertSame(123, $sUT->id);
        $this->assertSame($client, $sUT->getClient());
    }
}
