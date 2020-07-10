<?php

namespace Gitlab\Tests\Api;

class DeploymentsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllDeployments()
    {
        $expectedArray = [
            [
                'created_at' => '2016-08-11T07:36:40.222Z',
                'deployable' => [
                    'commit' => [
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
                    ],
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
                    'user' => [
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
                    ],
                ],
                'environment' => [
                    'external_url' => 'https://about.gitlab.com',
                    'id' => 9,
                    'name' => 'production',
                ],
                'id' => 41,
                'iid' => 1,
                'ref' => 'master',
                'sha' => '99d03678b90d914dbb1b109132516d71a4a03ea8',
                'user' => [
                    'avatar_url' => 'http://www.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=80&d=identicon',
                    'id' => 1,
                    'name' => 'Administrator',
                    'state' => 'active',
                    'username' => 'root',
                    'web_url' => 'http://localhost:3000/root',
                ],
            ],
            [
                'created_at' => '2016-08-11T11:32:35.444Z',
                'deployable' => [
                    'commit' => [
                        'author_email' => 'admin@example.com',
                        'author_name' => 'Administrator',
                        'created_at' => '2016-08-11T13:28:26.000+02:00',
                        'id' => 'a91957a858320c0e17f3a0eca7cfacbff50ea29a',
                        'message' => 'Merge branch \'rename-readme\' into \'master\'

Rename README



See merge request !2',
                        'short_id' => 'a91957a8',
                        'title' => 'Merge branch \'rename-readme\' into \'master\'
',
                    ],
                    'coverage' => null,
                    'created_at' => '2016-08-11T11:32:24.456Z',
                    'finished_at' => '2016-08-11T11:32:35.145Z',
                    'id' => 664,
                    'name' => 'deploy',
                    'ref' => 'master',
                    'runner' => null,
                    'stage' => 'deploy',
                    'started_at' => null,
                    'status' => 'success',
                    'tag' => false,
                    'user' => [
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
                    ],
                ],
                'environment' => [
                    'external_url' => 'https://about.gitlab.com',
                    'id' => 9,
                    'name' => 'production',
                ],
                'id' => 42,
                'iid' => 2,
                'ref' => 'master',
                'sha' => 'a91957a858320c0e17f3a0eca7cfacbff50ea29a',
                'user' => [
                    'avatar_url' => 'http://www.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=80&d=identicon',
                    'id' => 1,
                    'name' => 'Administrator',
                    'state' => 'active',
                    'username' => 'root',
                    'web_url' => 'http://localhost:3000/root',
                ],
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/deployments')
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->all(1));
    }

    /**
     * @test
     */
    public function shouldShowDeployment()
    {
        $expectedArray = [
            [
                'created_at' => '2016-08-11T11:32:35.444Z',
                'deployable' => [
                    'commit' => [
                        'author_email' => 'admin@example.com',
                        'author_name' => 'Administrator',
                        'created_at' => '2016-08-11T13:28:26.000+02:00',
                        'id' => 'a91957a858320c0e17f3a0eca7cfacbff50ea29a',
                        'message' => 'Merge branch \'rename-readme\' into \'master\'

Rename README



See merge request !2',
                        'short_id' => 'a91957a8',
                        'title' => 'Merge branch \'rename-readme\' into \'master\'
',
                    ],
                    'coverage' => null,
                    'created_at' => '2016-08-11T11:32:24.456Z',
                    'finished_at' => '2016-08-11T11:32:35.145Z',
                    'id' => 664,
                    'name' => 'deploy',
                    'ref' => 'master',
                    'runner' => null,
                    'stage' => 'deploy',
                    'started_at' => null,
                    'status' => 'success',
                    'tag' => false,
                    'user' => [
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
                    ],
                ],
                'environment' => [
                    'external_url' => 'https://about.gitlab.com',
                    'id' => 9,
                    'name' => 'production',
                ],
                'id' => 42,
                'iid' => 2,
                'ref' => 'master',
                'sha' => 'a91957a858320c0e17f3a0eca7cfacbff50ea29a',
                'user' => [
                    'avatar_url' => 'http://www.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=80&d=identicon',
                    'id' => 1,
                    'name' => 'Administrator',
                    'state' => 'active',
                    'username' => 'root',
                    'web_url' => 'http://localhost:3000/root',
                ],
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/deployments/42')
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->show(1, 42));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\Deployments';
    }
}
