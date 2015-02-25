<?php namespace Gitlab\Tests\Api;

class MilestonesTest extends ApiTestCase
{
    /**
     * @test
     */
    public function shouldGetAllMilestones()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'A milestone'),
            array('id' => 2, 'title' => 'Another milestone'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/milestones')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1));
    }

    /**
     * @test
     */
    public function shouldShowMilestone()
    {
        $expectedArray = array('id' => 1, 'name' => 'A milestone');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/milestones/2')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    /**
     * @test
     */
    public function shouldCreateMilestone()
    {
        $expectedArray = array('id' => 3, 'title' => 'A new milestone');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/milestones', array('description' => 'Some text', 'title' => 'A new milestone'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create(1, array('description' => 'Some text', 'title' => 'A new milestone')));
    }

    /**
     * @test
     */
    public function shouldUpdateMilestone()
    {
        $expectedArray = array('id' => 3, 'title' => 'Updated milestone');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/milestones/3', array('title' => 'Updated milestone', 'due_date' => '2015-04-01', 'state_event' => 'close'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(1, 3, array('title' => 'Updated milestone', 'due_date' => '2015-04-01', 'state_event' => 'close')));
    }

    /**
     * @test
     */
    public function shouldGetMilestonesIssues()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'An issue'),
            array('id' => 2, 'title' => 'Another issue'),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/milestones/3/issues')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->issues(1, 3));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\Milestones';
    }
}
