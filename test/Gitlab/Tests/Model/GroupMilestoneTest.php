<?php

namespace Gitlab\Tests\Model;

use Gitlab\Client;
use Gitlab\Model\Group;
use Gitlab\Model\GroupMilestone;
use PHPUnit\Framework\TestCase;

class GroupMilestoneTest extends TestCase
{
    public function testConstruct()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $group = $this->getMockBuilder(Group::class)
            ->disableOriginalConstructor()
            ->getMock();

        $groupMilestone = new GroupMilestone($group, 1, $client);

        $this->assertSame(1, $groupMilestone->id);
        $this->assertSame($group, $groupMilestone->group);
        $this->assertSame($client, $groupMilestone->getClient());
    }

    public function testFromArray()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $group = $this->getMockBuilder(Group::class)
            ->disableOriginalConstructor()
            ->getMock();

        $data = [
            'id' => 1,
            'iid' => 2,
            'group_id' => 3,
            'title' => 'Title',
            'description' => 'My Group Milestone',
            'state' => 'open',
            'created_at' => '2019-04-30T23:59:59.000Z',
            'updated_at' => '2019-04-30T23:59:59.000Z',
            'due_date' => '2019-05-10',
            'start_date' => '2019-05-03'
        ];

        $groupMilestone = GroupMilestone::fromArray($client, $group, $data);

        $this->assertInstanceOf(GroupMilestone::class, $groupMilestone);
        $this->assertSame($data['id'], $groupMilestone->id);
        $this->assertSame($data['iid'], $groupMilestone->iid);
        $this->assertSame($data['group_id'], $groupMilestone->group_id);
        $this->assertSame($data['title'], $groupMilestone->title);
        $this->assertSame($data['description'], $groupMilestone->description);
        $this->assertSame($data['state'], $groupMilestone->state);
        $this->assertSame($data['created_at'], $groupMilestone->created_at);
        $this->assertSame($data['updated_at'], $groupMilestone->updated_at);
        $this->assertSame($data['due_date'], $groupMilestone->due_date);
        $this->assertSame($data['start_date'], $groupMilestone->start_date);
    }
}
