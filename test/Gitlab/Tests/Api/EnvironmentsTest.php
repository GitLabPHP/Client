<?php namespace Gitlab\Tests\Api;

class EnvironmentsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllEnvironments()
    {
        $expectedArray = array(
            array(
                'id' => 1,
                'name' => 'review/fix-foo',
                'slug' => 'review-fix-foo-dfjre3',
                'external_url' => 'https://review-fix-foo-dfjre3.example.gitlab.com'
            ),
            array(
                'id' => 2,
                'name' => 'review/fix-bar',
                'slug' => 'review-fix-bar-dfjre4',
                'external_url' => 'https://review-fix-bar-dfjre4.example.gitlab.com'
            ),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/environments')
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->all(1));
    }

    /**
     * @test
     */
    public function shouldCreateEnvironment()
    {
        $expectedArray = array(
            array(
                'id' => 3,
                'name' => 'review/fix-baz',
                'slug' => 'review-fix-baz-dfjre5',
                'external_url' => 'https://review-fix-baz-dfjre5.example.gitlab.com'
            ),
        );

        $params = array(
            'name' => 'review/fix-baz',
            'slug' => 'review-fix-baz-dfjre5',
            'external_url' => 'https://review-fix-baz-dfjre5.example.gitlab.com'
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/environment', $params)
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->create(1, $params));
    }

    /**
     * @test
     */
    public function shouldRemoveEnvironment()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/environments/3')
            ->will($this->returnValue($expectedBool));
        $this->assertEquals($expectedBool, $api->remove(1, 3));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\Environments';
    }
}
