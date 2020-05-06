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

    /** @test */
    public function shouldFilterEnvironmentByName()
    {
        $expected = array(
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
            ->will($this->returnValue($expected));
        $this->assertEquals($expected, $api->all(1, ['name' => 'review/fix-bar']));
    }

    /**
     * @test
     */
    public function shouldGetSingleEnvironment()
    {
        $expected = array(
            'id' => 1,
            'name' => 'review/fix-foo',
            'slug' => 'review-fix-foo-dfjre3',
            'external_url' => 'https://review-fix-foo-dfjre3.example.gitlab.com',
            'latest_deployment' => array(
                'created_at' => '2016-08-11T07:36:40.222Z',
                'deployable' =>
                    array(
                        'commit' =>
                            array(
                                'author_email' => 'admin@example.com',
                                'author_name' => 'Administrator',
                                'created_at' => '2016-08-11T09:36:01.000+02:00',
                                'id' => '99d03678b90d914dbb1b109132516d71a4a03ea8',
                                'message' => 'Merge branch \'new-title\' into \'master\'

Update README



See merge request !1',
                                'short_id' => '99d03678',
                                'title' => 'Merge branch \'new-title\' into \'master\'
',
                            ),
                        'coverage' => null,
                        'created_at' => '2016-08-11T07:36:27.357Z',
                        'finished_at' => '2016-08-11T07:36:39.851Z',
                        'id' => 657,
                        'name' => 'deploy',
                        'ref' => 'master',
                        'runner' => null,
                        'stage' => 'deploy',
                        'started_at' => null,
                        'status' => 'success',
                        'tag' => false,
                        'user' =>
                            array(
                                'avatar_url' => 'http://www.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=80&d=identicon',
                                'bio' => null,
                                'created_at' => '2016-08-11T07:09:20.351Z',
                                'id' => 1,
                                'linkedin' => '',
                                'location' => null,
                                'name' => 'Administrator',
                                'skype' => '',
                                'state' => 'active',
                                'twitter' => '',
                                'username' => 'root',
                                'web_url' => 'http://localhost:3000/root',
                                'website_url' => '',
                            ),
                    ),
                'id' => 41,
                'iid' => 1,
                'ref' => 'master',
                'sha' => '99d03678b90d914dbb1b109132516d71a4a03ea8',
                'user' =>
                    array(
                        'avatar_url' => 'http://www.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=80&d=identicon',
                        'id' => 1,
                        'name' => 'Administrator',
                        'state' => 'active',
                        'username' => 'root',
                        'web_url' => 'http://localhost:3000/root',
                    ),
            )
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/environments/1')
            ->will($this->returnValue($expected));
        $this->assertEquals($expected, $api->show(1, 1));
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

    /**
     * @test
     */
    public function shouldStopEnvironment()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/environments/3/stop')
            ->will($this->returnValue($expectedBool));
        $this->assertEquals($expectedBool, $api->stop(1, 3));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\Environments';
    }
}
