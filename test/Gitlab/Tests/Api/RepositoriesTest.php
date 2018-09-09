<?php namespace Gitlab\Tests\Api;

use Gitlab\Api\AbstractApi;

class RepositoriesTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetBranches()
    {
        $expectedArray = array(
            array('name' => 'master'),
            array('name' => 'develop')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/branches')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->branches(1));
    }

    /**
     * @test
     */
    public function shouldGetBranch()
    {
        $expectedArray = array('name' => 'master');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/branches/master')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->branch(1, 'master'));
    }

    /**
     * @test
     */
    public function shouldCreateBranch()
    {
        $expectedArray = array('name' => 'feature');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/branches', array('branch' => 'feature', 'ref' => 'master'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createBranch(1, 'feature', 'master'));
    }

    /**
     * @test
     */
    public function shouldDeleteBranch()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/repository/branches/feature%2FTEST-15')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->deleteBranch(1, 'feature/TEST-15'));
    }

    /**
     * @test
     */
    public function shouldProtectBranch()
    {
        $expectedArray = array('name' => 'master');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/repository/branches/master/protect', array('developers_can_push' => false, 'developers_can_merge' => false))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->protectBranch(1, 'master'));
    }

    /**
     * @test
     */
    public function shouldProtectBranchWithPermissions()
    {
        $expectedArray = array('name' => 'master');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/repository/branches/master/protect', array('developers_can_push' => true, 'developers_can_merge' => true))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->protectBranch(1, 'master', true, true));
    }

    /**
     * @test
     */
    public function shouldUnprotectBranch()
    {
        $expectedArray = array('name' => 'master');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/repository/branches/master/unprotect')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->unprotectBranch(1, 'master'));
    }

    /**
     * @test
     */
    public function shouldGetTags()
    {
        $expectedArray = array(
            array('name' => '1.0'),
            array('name' => '1.1')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/tags')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->tags(1));
    }

    /**
     * @test
     */
    public function shouldCreateTag()
    {
        $expectedArray = array('name' => '1.0');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/tags', array(
                'tag_name' => '1.0',
                'ref' => 'abcd1234',
                'message' => '1.0 release'
            ))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createTag(1, '1.0', 'abcd1234', '1.0 release'));
    }

    /**
     * @test
     */
    public function shouldCreateRelease()
    {
        $project_id  = 1;
        $tagName     = 'sometag';
        $description = '1.0 release';

        $expectedArray = array( 'name' => $tagName );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/' . $project_id . '/repository/tags/' . $tagName . '/release', array(
                'id' => $project_id,
                'tag_name' => $tagName,
                'description' => $description
            ))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createRelease($project_id, $tagName, $description));
    }

    /**
     * @test
     */
    public function shouldUpdateRelease()
    {
        $project_id  = 1;
        $tagName     = 'sometag';
        $description = '1.0 release';

        $expectedArray = array( 'description' => $tagName );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/' . $project_id . '/repository/tags/' . $tagName . '/release', array(
                'id' => $project_id,
                'tag_name' => $tagName,
                'description' => $description
            ))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateRelease($project_id, $tagName, $description));
    }

    /**
     * @test
     */
    public function shouldGetCommits()
    {
        $expectedArray = array(
            array('id' => 'abcd1234', 'title' => 'A commit'),
            array('id' => 'efgh5678', 'title' => 'Another commit')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits', array())
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->commits(1));
    }

    /**
     * @test
     */
    public function shouldGetCommitsWithParams()
    {
        $expectedArray = array(
            array('id' => 'abcd1234', 'title' => 'A commit'),
            array('id' => 'efgh5678', 'title' => 'Another commit')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits', array('page' => 2, 'per_page' => 25, 'ref_name' => 'master', 'all' => true, 'with_stats' => true))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->commits(1, ['page' => 2, 'per_page' => 25, 'ref_name' => 'master', 'all' => true, 'with_stats' => true]));
    }

    /**
     * @test
     */
    public function shouldGetCommitsWithTimeParams()
    {
        $expectedArray = [
            ['id' => 'abcd1234', 'title' => 'A commit'],
            ['id' => 'efgh5678', 'title' => 'Another commit']
        ];

        $since = new \DateTime('2018-01-01 00:00:00');
        $until = new \DateTime('2018-01-31 00:00:00');

        $expectedWithArray = [
            'since' => $since->format(DATE_ATOM),
            'until' => $until->format(DATE_ATOM),
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits', $expectedWithArray)
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->commits(1, ['since' => $since, 'until' => $until]));
    }

    /**
     * @test
     */
    public function shouldGetCommit()
    {
        $expectedArray = array('id' => 'abcd1234', 'title' => 'A commit');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits/abcd1234')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->commit(1, 'abcd1234'));
    }

    /**
     * @test
     */
    public function shouldGetCommitRefs()
    {
        $expectedArray = [
            ['type' => 'branch', 'name' => 'master'],
            ['type' => 'tag', 'name' => 'v1.1.0'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits/abcd1234/refs')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->commitRefs(1, 'abcd1234'));
    }

    /**
     * @test
     */
    public function shouldCreateCommit()
    {
        $expectedArray = array('title' => 'Initial commit.', 'author_name' => 'John Doe', 'author_email' => 'john@example.com');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/commits')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createCommit(1, [
            'branch' => 'master',
            'commit_message' => 'Initial commit.',
            'actions' => [
                [
                    'action' => 'create',
                    'file_path' => 'README.md',
                    'content' => '# My new project',
                ],
                [
                    'action' => 'create',
                    'file_path' => 'LICENSE',
                    'content' => 'MIT License...',
                ],
            ],
            'author_name' => 'John Doe',
            'author_email' => 'john@example.com',
        ]));
    }

    /**
     * @test
     */
    public function shouldGetCommitComments()
    {
        $expectedArray = array(
            array('note' => 'A commit message'),
            array('note' => 'Another commit message')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits/abcd1234/comments')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->commitComments(1, 'abcd1234'));
    }

    /**
     * @test
     */
    public function shouldCreateCommitComment()
    {
        $expectedArray = array('id' => 2, 'title' => 'A new comment');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/commits/abcd1234/comments', array('note' => 'A new comment'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createCommitComment(1, 'abcd1234', 'A new comment'));
    }

    /**
     * @test
     */
    public function shouldCreateCommitCommentWithParams()
    {
        $expectedArray = array('id' => 2, 'title' => 'A new comment');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/commits/abcd1234/comments', array(
                'note' => 'A new comment',
                'path' => '/some/file.txt',
                'line' => 123, 'line_type' => 'old'
            ))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createCommitComment(1, 'abcd1234', 'A new comment', array(
            'path' => '/some/file.txt',
            'line' => 123,
            'line_type' => 'old'
        )));
    }

    /**
     * @test
     */
    public function shouldCompare()
    {
        $expectedArray = array('commit' => 'object');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/compare?from=master&to=feature')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->compare(1, 'master', 'feature'));
    }

    /**
     * @test
     */
    public function shouldGetDiff()
    {
        $expectedArray = array(
            array('diff' => '--- ...'),
            array('diff' => '+++ ...')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits/abcd1234/diff')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->diff(1, 'abcd1234'));
    }

    /**
     * @test
     */
    public function shouldGetTree()
    {
        $expectedArray = array(
            array('name' => 'file1.txt'),
            array('name' => 'file2.csv')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/tree')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->tree(1));
    }

    /**
     * @test
     */
    public function shouldGetTreeWithParams()
    {
        $expectedArray = array(
            array('name' => 'dir/file1.txt'),
            array('name' => 'dir/file2.csv')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/tree', array('path' => 'dir/', 'ref_name' => 'master'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->tree(1, array('path' => 'dir/', 'ref_name' => 'master')));
    }

    /**
     * @test
     */
    public function shouldGetContributors()
    {
        $expectedArray = array(
            array('name' => 'Matt'),
            array('name' => 'Bob')
        );

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/contributors')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->contributors(1));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\Repositories';
    }
}
