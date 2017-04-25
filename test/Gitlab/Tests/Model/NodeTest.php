<?php

namespace Gitlab\Tests\Model;

use Gitlab\Model\Node;
use Gitlab\Model\Project;
use Gitlab\Tests\TestCase;


class NodeTest extends TestCase
{
    protected $client;
    protected $project;

    public function setUp()
    {
        parent::setUp();
        $this->client = $this->getClientMock();
        $this->project = $this->getMock(Project::class);

    }


    /**
     * @test
     */
    public function verifyNewModelIsEmpty()
    {
        $node = new Node($this->project, null, $this->client);
        $this->assertEquals($this->project, $node->project);
        $this->assertEquals(null, $node->id);
        $this->assertEquals($this->client, $node->getClient());
    }

    /**
     * @test
     */
    public function verifyLoadFromArrayLoadsAllData() {
        /** @var \Gitlab\Model\Node $node */

        $arrayData = array(['id'=>'1','name'=>'a node','type'=>'group','path'=>'src']);
        $node =  Node::fromArray($this->client,$this->project,$arrayData);
        $this->assertEquals($arrayData['id'],$node->id);
        $this->assertEquals($arrayData['name'],$node->name);
        $this->assertEquals($arrayData['type'],$node->type);
        $this->assertEquals($arrayData['path'],$node->path);
    }




}
