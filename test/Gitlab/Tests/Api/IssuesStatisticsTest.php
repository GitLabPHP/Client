<?php

namespace Gitlab\Tests\Api;

use Gitlab\Api\IssuesStatistics;

class IssuesStatisticsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAll()
    {
        $expectedArray = [];

        $now = new \DateTime();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('issues_statistics', [
                'milestone' => '',
                'labels' => '',
                'scope' => 'created-by-me',
                'author_id' => 1,
                'author_username' => '',
                'assignee_id' => 1,
                'assignee_username' => '',
                'my_reaction_emoji' => '',
                'search' => '',
                'created_after' => $now->format('c'),
                'created_before' => $now->format('c'),
                'updated_after' => $now->format('c'),
                'updated_before' => $now->format('c'),
                'confidential' => 'false',
            ])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->all([
            'milestone' => '',
            'labels' => '',
            'scope' => 'created-by-me',
            'author_id' => 1,
            'author_username' => '',
            'assignee_id' => 1,
            'assignee_username' => '',
            'my_reaction_emoji' => '',
            'search' => '',
            'created_after' => $now,
            'created_before' => $now,
            'updated_after' => $now,
            'updated_before' => $now,
            'confidential' => false,
        ]));
    }

    /**
     * @test
     */
    public function shouldGetProject()
    {
        $expectedArray = [];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues_statistics', [])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->project(1, []));
    }

    /**
     * @test
     */
    public function shouldGetGroup()
    {
        $expectedArray = [];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('groups/1/issues_statistics', [])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->group(1, []));
    }

    protected function getApiClass()
    {
        return IssuesStatistics::class;
    }
}
