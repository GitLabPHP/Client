<?php namespace Gitlab\Tests\Api;

class CommitsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllCommits()
    {
        $expectedArray = array(
            array(
                "id" => "4b4a1c624577d22c598a691b8f6d90f822d88f1b",
                "short_id" => "4b4a1c62",
                "title" => "Update README.md",
                "created_at" => "2017-09-19T13:38:05.000+00:00",
                "parent_ids" => array(
                    "586e8b3048b0d93319fada5bd4d7a6f5f26f5bb8",
                ),
                "message" => "Update README.md",
                "author_name" => "Administrator",
                "author_email" => "admin@example.com",
                "authored_date" => "2017-09-19T13:38:05.000+00:00",
                "committer_name" => "Administrator",
                "committer_email" => "admin@example.com",
                "committed_date" => "2017-09-19T13:38:05.000+00:00",
            ),
            array(
                "id" => "586e8b3048b0d93319fada5bd4d7a6f5f26f5bb8",
                "short_id" => "586e8b30",
                "title" => "Add readme.md",
                "created_at" => "2017-09-11T07:44:07.000+00:00",
                "parent_ids" => array(),
                "message" => "Add readme.md",
                "author_name" => "Administrator",
                "author_email" => "admin@example.com",
                "authored_date" => "2017-09-11T07:44:07.000+00:00",
                "committer_name" => "Administrator",
                "committer_email" => "admin@example.com",
                "committed_date" => "2017-09-11T07:44:07.000+00:00",
            ),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits')
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->all(1));
    }

    /**
     * @test
     */
    public function shouldShowCommit()
    {
        $expectedArray = array(
            "id" => "4b4a1c624577d22c598a691b8f6d90f822d88f1b",
            "short_id" => "4b4a1c62",
            "title" => "Update README.md",
            "created_at" => "2017-09-19T13:38:05.000+00:00",
            "parent_ids" => array(
                "586e8b3048b0d93319fada5bd4d7a6f5f26f5bb8",
            ),
            "message" => "Update README.md",
            "author_name" => "Administrator",
            "author_email" => "admin@example.com",
            "authored_date" => "2017-09-19T13:38:05.000+00:00",
            "committer_name" => "Administrator",
            "committer_email" => "admin@example.com",
            "committed_date" => "2017-09-19T13:38:05.000+00:00",
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits/master')
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->show(1, 'master'));
    }

    /**
     * @test
     */
    public function shouldGetDiffCommit()
    {
        $diff = <<<DIFF
--- a/README.md
+++ b/README.md
@@ -1,4 +1,4 @@
hello world
-41
\ No newline at end of file
+42
\ No newline at end of file
DIFF;

        $expectedArray = array(
            array(
                array(
                    "diff" => $diff,
                    "new_path" => "README.md",
                    "old_path" => "README.md",
                    "a_mode" => "100644",
                    "b_mode" => "100644",
                    "new_file" => false,
                    "renamed_file" => false,
                    "deleted_file" => false,
                    "too_large" => null,
                ),
            ),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits/master/diff')
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->diff(1, 'master'));
    }

    /**
     * @test
     */
    public function shouldGetCommentsCommit()
    {
        $expectedArray = array(
            array(
                "note" => "comment",
                "path" => null,
                "line" => null,
                "line_type" => null,
                "author" => array(
                    "name" => "Administrator",
                    "username" => "root",
                    "id" => 1,
                    "state" => "active",
                    "avatar_url" => "http://www.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?s=80&d=identicon",
                    "web_url" => "http://gitlab.docker/root",
                ),
                "created_at" => "2017-09-19T14:02:26.721Z",
            ),
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits/master/comments')
            ->will($this->returnValue($expectedArray));
        $this->assertEquals($expectedArray, $api->comments(1, 'master'));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\Commits';
    }
}
