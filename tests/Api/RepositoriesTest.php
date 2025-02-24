<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Tests\Api;

use Gitlab\Api\Repositories;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class RepositoriesTest extends TestCase
{
    #[Test]
    public function shouldGetBranches(): void
    {
        $expectedArray = [
            ['name' => 'master'],
            ['name' => 'develop'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/branches', ['search' => '^term'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->branches(1, ['search' => '^term']));
    }

    #[Test]
    public function shouldGetBranch(): void
    {
        $expectedArray = ['name' => 'master'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/branches/master')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->branch(1, 'master'));
    }

    #[Test]
    public function shouldCreateBranch(): void
    {
        $expectedArray = ['name' => 'feature'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/branches', ['branch' => 'feature', 'ref' => 'master'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->createBranch(1, 'feature', 'master'));
    }

    #[Test]
    public function shouldDeleteBranch(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/repository/branches/feature%2FTEST-15')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->deleteBranch(1, 'feature/TEST-15'));
    }

    #[Test]
    public function shouldProtectBranch(): void
    {
        $expectedArray = ['name' => 'master'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/repository/branches/master/protect', ['developers_can_push' => false, 'developers_can_merge' => false])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->protectBranch(1, 'master'));
    }

    #[Test]
    public function shouldProtectBranchWithPermissions(): void
    {
        $expectedArray = ['name' => 'master'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/repository/branches/master/protect', ['developers_can_push' => true, 'developers_can_merge' => true])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->protectBranch(1, 'master', true, true));
    }

    #[Test]
    public function shouldUnprotectBranch(): void
    {
        $expectedArray = ['name' => 'master'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/repository/branches/master/unprotect')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->unprotectBranch(1, 'master'));
    }

    #[Test]
    public function shouldGetTags(): void
    {
        $expectedArray = [
            ['name' => '1.0'],
            ['name' => '1.1'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/tags')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->tags(1, ['search' => '^term']));
    }

    #[Test]
    public function shouldCreateTag(): void
    {
        $expectedArray = ['name' => '1.0'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/tags', [
                'tag_name' => '1.0',
                'ref' => 'abcd1234',
                'message' => '1.0 release',
            ])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->createTag(1, '1.0', 'abcd1234', '1.0 release'));
    }

    #[Test]
    public function shouldCreateRelease(): void
    {
        $project_id = 1;
        $tagName = 'sometag';
        $description = '1.0 release';

        $expectedArray = ['name' => $tagName];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/'.$project_id.'/releases', [
                'id' => $project_id,
                'tag_name' => $tagName,
                'description' => $description,
            ])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->createRelease($project_id, $tagName, $description));
    }

    #[Test]
    public function shouldUpdateRelease(): void
    {
        $project_id = 1;
        $tagName = 'sometag';
        $description = '1.0 release';

        $expectedArray = ['description' => $tagName];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/'.$project_id.'/releases/'.$tagName, [
                'id' => $project_id,
                'tag_name' => $tagName,
                'description' => $description,
            ])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->updateRelease($project_id, $tagName, $description));
    }

    #[Test]
    public function shouldGetReleases(): void
    {
        $project_id = 1;

        $expectedArray = [
            [
                'tag_name' => 'v0.2',
                'description' => '## CHANGELOG\r\n\r\n- Escape label and milestone titles to prevent XSS in GFM autocomplete. !2740\r\n- Prevent private snippets from being embeddable.\r\n- Add subresources removal to member destroy service.',
                'name' => 'Awesome app v0.2 beta',
            ],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/releases')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->releases($project_id));
    }

    #[Test]
    public function shouldGetCommits(): void
    {
        $expectedArray = [
            ['id' => 'abcd1234', 'title' => 'A commit'],
            ['id' => 'efgh5678', 'title' => 'Another commit'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits', [])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->commits(1));
    }

    #[Test]
    public function shouldGetCommitsWithParams(): void
    {
        $expectedArray = [
            ['id' => 'abcd1234', 'title' => 'A commit'],
            ['id' => 'efgh5678', 'title' => 'Another commit'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits', ['page' => 2, 'per_page' => 25, 'ref_name' => 'master', 'all' => 'true', 'with_stats' => 'true', 'path' => 'file_path/file_name'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->commits(1, ['page' => 2, 'per_page' => 25, 'ref_name' => 'master', 'all' => true, 'with_stats' => true, 'path' => 'file_path/file_name']));
    }

    #[Test]
    public function shouldGetCommitsWithTimeParams(): void
    {
        $expectedArray = [
            ['id' => 'abcd1234', 'title' => 'A commit'],
            ['id' => 'efgh5678', 'title' => 'Another commit'],
        ];

        $since = new \DateTime('2018-01-01 00:00:00');
        $until = new \DateTime('2018-01-31 00:00:00');

        $expectedWithArray = [
            'since' => $since->format(\DATE_ATOM),
            'until' => $until->format(\DATE_ATOM),
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits', $expectedWithArray)
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->commits(1, ['since' => $since, 'until' => $until]));
    }

    #[Test]
    public function shouldGetCommit(): void
    {
        $expectedArray = ['id' => 'abcd1234', 'title' => 'A commit'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits/abcd1234')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->commit(1, 'abcd1234'));
    }

    #[Test]
    public function shouldGetCommitRefs(): void
    {
        $expectedArray = [
            ['type' => 'branch', 'name' => 'master'],
            ['type' => 'tag', 'name' => 'v1.1.0'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits/abcd1234/refs')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->commitRefs(1, 'abcd1234'));
    }

    #[Test]
    #[DataProvider('dataGetCommitRefsWithParams')]
    public function shouldGetCommitRefsWithParams(string $type, array $expectedArray): void
    {
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits/abcd1234/refs', ['type' => $type])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->commitRefs(1, 'abcd1234', ['type' => $type]));
    }

    public static function dataGetCommitRefsWithParams(): array
    {
        return [
            'type_tag' => [
                'type' => Repositories::TYPE_TAG,
                'expectedArray' => [['type' => 'tag', 'name' => 'v1.1.0']],
            ],
            'type_branch' => [
                'type' => Repositories::TYPE_BRANCH,
                'expectedArray' => [['type' => 'branch', 'name' => 'master']],
            ],
        ];
    }

    #[Test]
    public function shouldCreateCommit(): void
    {
        $expectedArray = ['title' => 'Initial commit.', 'author_name' => 'John Doe', 'author_email' => 'john@example.com'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/commits')
            ->willReturn($expectedArray)
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

    #[Test]
    public function shouldRevertCommit(): void
    {
        $expectedArray = ['title' => 'Initial commit.', 'author_name' => 'John Doe', 'author_email' => 'john@example.com'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/commits/abcd1234/revert')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->revertCommit(1, 'develop', 'abcd1234'));
    }

    #[Test]
    public function shouldGetCommitComments(): void
    {
        $expectedArray = [
            ['note' => 'A commit message'],
            ['note' => 'Another commit message'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits/abcd1234/comments')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->commitComments(1, 'abcd1234'));
    }

    #[Test]
    public function shouldCreateCommitComment(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'A new comment'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/commits/abcd1234/comments', ['note' => 'A new comment'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->createCommitComment(1, 'abcd1234', 'A new comment'));
    }

    #[Test]
    public function shouldCreateCommitCommentWithParams(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'A new comment'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/commits/abcd1234/comments', [
                'note' => 'A new comment',
                'path' => '/some/file.txt',
                'line' => 123, 'line_type' => 'old',
            ])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->createCommitComment(1, 'abcd1234', 'A new comment', [
            'path' => '/some/file.txt',
            'line' => 123,
            'line_type' => 'old',
        ]));
    }

    #[Test]
    public function shouldCompareStraight(): void
    {
        $expectedArray = ['commit' => 'object'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/compare', ['from' => 'master', 'to' => 'feature', 'straight' => 'true'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->compare(1, 'master', 'feature', true));
    }

    #[Test]
    public function shouldNotCompareStraight(): void
    {
        $expectedArray = ['commit' => 'object'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/compare', ['from' => 'master', 'to' => 'feature', 'straight' => 'false'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->compare(1, 'master', 'feature'));
    }

    #[Test]
    public function shouldCompareComplexBranchName(): void
    {
        $expectedArray = ['commit' => 'object'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/compare', ['from' => 'master', 'to' => 'feature/760.fake-branch', 'straight' => 'true'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->compare(1, 'master', 'feature/760.fake-branch', true));
    }

    #[Test]
    public function shouldCompareWithFromProjectId(): void
    {
        $expectedArray = ['commit' => 'object'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/compare', ['from' => 'master', 'to' => 'feature', 'straight' => 'true', 'from_project_id' => '123'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->compare(1, 'master', 'feature', true, '123'));
    }

    #[Test]
    public function shouldGetDiff(): void
    {
        $expectedArray = [
            ['diff' => '--- ...'],
            ['diff' => '+++ ...'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/commits/abcd1234/diff')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->diff(1, 'abcd1234'));
    }

    #[Test]
    public function shouldGetTree(): void
    {
        $expectedArray = [
            ['name' => 'file1.txt'],
            ['name' => 'file2.csv'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/tree')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->tree(1));
    }

    #[Test]
    public function shouldGetTreeWithParams(): void
    {
        $expectedArray = [
            ['name' => 'dir/file1.txt'],
            ['name' => 'dir/file2.csv'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/tree', ['path' => 'dir/', 'ref_name' => 'master'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->tree(1, ['path' => 'dir/', 'ref_name' => 'master']));
    }

    #[Test]
    public function shouldGetContributors(): void
    {
        $expectedArray = [
            ['name' => 'Matt'],
            ['name' => 'Bob'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/contributors')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->contributors(1));
    }

    #[Test]
    public function shouldGetMergeBase(): void
    {
        $expectedArray = [
            'id' => 'abcd1234abcd1234abcd1234abcd1234abcd1234',
            'short_id' => 'abcd1234',
            'title' => 'A commit',
            'created_at' => '2018-01-01T00:00:00.000Z',
            'parent_ids' => [
                'efgh5678efgh5678efgh5678efgh5678efgh5678',
            ],
            'message' => 'A commit',
            'author_name' => 'Jane Doe',
            'author_email' => 'jane@example.org',
            'authored_date' => '2018-01-01T00:00:00.000Z',
            'committer_name' => 'Jane Doe',
            'committer_email' => 'jane@example.org',
            'committed_date' => '2018-01-01T00:00:00.000Z',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/merge_base', ['refs' => ['efgh5678efgh5678efgh5678efgh5678efgh5678', '1234567812345678123456781234567812345678']])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->mergeBase(1, ['efgh5678efgh5678efgh5678efgh5678efgh5678', '1234567812345678123456781234567812345678']));
    }

    #[Test]
    public function shouldCherryPick(): void
    {
        $expectedArray = [
            'id' => 'abcd1234abcd1234abcd1234abcd1234abcd1234',
            'short_id' => 'abcd1234',
            'title' => 'A commit',
            'author_name' => 'Example User',
            'author_email' => 'jane@example.org',
            'authored_date' => '2018-01-01T00:00:00.000Z',
            'created_at' => '2018-01-01T00:00:00.000Z',
            'committer_name' => 'Jane Doe',
            'committer_email' => 'jane@example.org',
            'committed_date' => '2018-01-01T00:00:00.000Z',
            'message' => 'A commit',
            'parent_ids' => [
                'efgh5678efgh5678efgh5678efgh5678efgh5678',
            ],
            'web_url' => 'https://gitlab.example.com/thedude/gitlab-foss/-/commit/abcd1234abcd1234abcd1234abcd1234abcd1234',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/commits/123456123456/cherry_pick', ['branch' => 'feature_branch'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->cherryPick(1, '123456123456', ['branch' => 'feature_branch']));
    }

    protected function getApiClass(): string
    {
        return Repositories::class;
    }
}
