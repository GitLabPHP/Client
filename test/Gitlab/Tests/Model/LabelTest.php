<?php

namespace Gitlab\Tests\Model;

use Gitlab\Client;
use Gitlab\Model\Label;
use Gitlab\Model\Project;
use PHPUnit\Framework\TestCase;

class LabelTest extends TestCase
{
    public function testCorrectConstructWithoutClient()
    {
        $project = new Project();

        $sUT = new Label($project);

        $this->assertSame(null, $sUT->getClient());
    }

    public function testCorrectConstruct()
    {
        $project = new Project();
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $sUT = new Label($project, $client);

        $this->assertSame($client, $sUT->getClient());
    }

    public function testFromArray()
    {
        $project = new Project();
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $sUT = Label::fromArray($client, $project, ['color' => '#FF0000', 'name' => 'Testing', 'id' => 123]);

        $this->assertSame('#FF0000', $sUT->color);
        $this->assertSame('Testing', $sUT->name);
        $this->assertSame(123, $sUT->id);
        $this->assertSame($client, $sUT->getClient());
    }
}
