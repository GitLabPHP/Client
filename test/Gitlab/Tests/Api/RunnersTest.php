<?php namespace Gitlab\Tests\Api;

use Gitlab\Api\Jobs;
use GuzzleHttp\Psr7\Response;

class RunnersTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllProjects()
    {
        $expectedArray = $this->getMultipleRunnersData();

        $api = $this->getMultipleRunnersRequestMock('runners/all', $expectedArray);

        $this->assertEquals($expectedArray, $api->all());
    }

    /**
     * @test
     */
    public function shouldGetOwnedProjects()
    {
        $expectedArray = $this->getMultipleRunnersData();

        $api = $this->getMultipleRunnersRequestMock('runners', $expectedArray);

        $this->assertEquals($expectedArray, $api->owned());
    }

    /**
     * @test
     */
    public function shouldRunnerDetails()
    {
        $expectedArray = array(
            "active"          => true,
            "architecture"    => null,
            "description"     => "test-1-20150125",
            "id"              => 6,
            "ip_address"      => "127.0.0.1",
            "is_shared"       => false,
            "contacted_at"    => "2016-01-25T16:39:48.066Z",
            "name"            => null,
            "online"          => true,
            "status"          => "online",
            "platform"        => null,
            "projects"        => array(
                array(
                    "id"                  => 1,
                    "name"                => "GitLab Community Edition",
                    "name_with_namespace" => "GitLab.org / GitLab Community Edition",
                    "path"                => "gitlab-ce",
                    "path_with_namespace" => "gitlab-org/gitlab-ce"
                )
            ),
            "token"           => "205086a8e3b9a2b818ffac9b89d102",
            "revision"        => null,
            "tag_list"        => array(
                "ruby",
                "mysql"
            ),
            "version"         => null,
            "access_level"    => "ref_protected",
            "maximum_timeout" => 3600
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('runners/1')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->details(1));
    }

    /**
     * @test
     */
    public function shouldGetJobs()
    {
        $expectedArray = array(
            array(
                "id"          => 2,
                "ip_address"  => "127.0.0.1",
                "status"      => "running",
                "stage"       => "test",
                "name"        => "test",
                "ref"         => "master",
                "tag"         => false,
                "coverage"    => null,
                "created_at"  => "2017-11-16T08:50:29.000Z",
                "started_at"  => "2017-11-16T08:51:29.000Z",
                "finished_at" => "2017-11-16T08:53:29.000Z",
                "duration"    => 120,
                "user"        => array(
                    "id"           => 1,
                    "name"         => "John Doe2",
                    "username"     => "user2",
                    "state"        => "active",
                    "avatar_url"   => "http://www.gravatar.com/avatar/c922747a93b40d1ea88262bf1aebee62?s=80&d=identicon",
                    "web_url"      => "http://localhost/user2",
                    "created_at"   => "2017-11-16T18:38:46.000Z",
                    "bio"          => null,
                    "location"     => null,
                    "public_email" => "",
                    "skype"        => "",
                    "linkedin"     => "",
                    "twitter"      => "",
                    "website_url"  => "",
                    "organization" => null
                ),
                "commit"      => array(
                    "id"              => "97de212e80737a608d939f648d959671fb0a0142",
                    "short_id"        => "97de212e",
                    "title"           => "Update configuration\r",
                    "created_at"      => "2017-11-16T08:50:28.000Z",
                    "parent_ids"      => array(
                        "1b12f15a11fc6e62177bef08f47bc7b5ce50b141",
                        "498214de67004b1da3d820901307bed2a68a8ef6"
                    ),
                    "message"         => "See merge request !123",
                    "author_name"     => "John Doe2",
                    "author_email"    => "user2@example.org",
                    "authored_date"   => "2017-11-16T08:50:27.000Z",
                    "committer_name"  => "John Doe2",
                    "committer_email" => "user2@example.org",
                    "committed_date"  => "2017-11-16T08:50:27.000Z"
                ),
                "pipeline"    => array(
                    "id"     => 2,
                    "sha"    => "97de212e80737a608d939f648d959671fb0a0142",
                    "ref"    => "master",
                    "status" => "running"
                ),
                "project"     => array(
                    "id"                  => 1,
                    "description"         => null,
                    "name"                => "project1",
                    "name_with_namespace" => "John Doe2 / project1",
                    "path"                => "project1",
                    "path_with_namespace" => "namespace1/project1",
                    "created_at"          => "2017-11-16T18:38:46.620Z"
                )
            )
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('runners/1/jobs')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->jobs(1));
    }

    protected function getMultipleRunnersRequestMock($path, $expectedArray = array(), $expectedParameters = array())
    {
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with($path, $expectedParameters)
            ->will($this->returnValue($expectedArray));

        return $api;
    }

    protected function getMultipleRunnersData()
    {
        return array(
            array(
                "active"      => true,
                "description" => "test-1-20150125",
                "id"          => 6,
                "is_shared"   => false,
                "ip_address"  => "127.0.0.1",
                "name"        => null,
                "online"      => true,
                "status"      => "online"
            ),
            array(
                "active"      => true,
                "description" => "test-2-20150125",
                "id"          => 8,
                "ip_address"  => "127.0.0.1",
                "is_shared"   => false,
                "name"        => null,
                "online"      => false,
                "status"      => "offline"
            )
        );
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\Runners';
    }

}
