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

use Gitlab\Api\IssueLinks;
use PHPUnit\Framework\Attributes\Test;

class IssueLinksTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getApiClass(): string
    {
        return IssueLinks::class;
    }

    #[Test]
    public function shouldGetIssueLinks(): void
    {
        $expectedArray = [
            ['issue_link_id' => 100],
            ['issue_link_id' => 101],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/issues/10/links')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(1, 10));
    }

    #[Test]
    public function shouldCreateIssueLink(): void
    {
        $expectedArray = [
            'source_issue' => ['iid' => 10, 'project_id' => 1],
            'target_issue' => ['iid' => 20, 'project_id' => 2],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/issues/10/links', ['target_project_id' => 2, 'target_issue_iid' => 20])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->create(1, 10, 2, 20));
    }

    #[Test]
    public function shouldRemoveIssueLink(): void
    {
        $expectedArray = [
            'source_issue' => ['iid' => 10, 'project_id' => 1],
            'target_issue' => ['iid' => 20, 'project_id' => 2],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/issues/10/links/100')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->remove(1, 10, 100));
    }
}
