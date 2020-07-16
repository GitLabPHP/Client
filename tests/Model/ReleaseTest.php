<?php

declare(strict_types=1);

namespace Gitlab\Tests\Model;

use Gitlab\Client;
use Gitlab\Model\Project;
use Gitlab\Model\Release;
use PHPUnit\Framework\TestCase;

class ReleaseTest extends TestCase
{
    public function testFromArray()
    {
        $params = [
            'tag_name' => 'v1.0.0',
            'description' => 'Amazing release. Wow',
        ];

        $project = new Project();
        $client = $this->createMock(Client::class);

        $release = Release::fromArray($client, $params);

        $this->assertSame($params['tag_name'], $release->tag_name);
        $this->assertSame($params['description'], $release->description);
    }
}
