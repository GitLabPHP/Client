<?php

namespace Gitlab\Tests\Model;

use Gitlab\Api\Tags;
use Gitlab\Client;
use Gitlab\Api\Projects;
use Gitlab\Model\Release;
use Gitlab\Model\Project;
use PHPUnit\Framework\TestCase;

class ReleaseTest extends TestCase
{
    public function testFromArray()
    {
        $params = array(
            'tag_name' => 'v1.0.0',
            'description' => 'Amazing release. Wow',
        );

        $project = new Project();
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $release = Release::fromArray($client, $params);

        $this->assertSame($params['tag_name'], $release->tag_name);
        $this->assertSame($params['description'], $release->description);
    }
}
