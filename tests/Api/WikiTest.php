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

use Gitlab\Api\Wiki;
use PHPUnit\Framework\Attributes\Test;

class WikiTest extends TestCase
{
    #[Test]
    public function shouldCreateWiki(): void
    {
        $expectedArray = [
            'format' => 'markdown',
            'slug' => 'Test-Wiki',
            'title' => 'Test Wiki',
            'content' => 'This is the test Wiki',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/wikis', [
                'format' => 'markdown',
                'title' => 'Test Wiki',
                'content' => 'This is the test Wiki',
            ])
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->create(
            1,
            [
                'format' => 'markdown',
                'title' => 'Test Wiki',
                'content' => 'This is the test Wiki',
            ]
        ));
    }

    #[Test]
    public function shouldShowWiki(): void
    {
        $expectedArray = [
            'slug' => 'Test-Wiki',
            'title' => 'Test Wiki',
            'format' => 'markdown',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/wikis/Test-Wiki')
            ->willReturn($expectedArray);

        $this->assertEquals($expectedArray, $api->show(1, 'Test-Wiki'));
    }

    #[Test]
    public function shouldShowAllWiki(): void
    {
        $expectedArray = [
            'slug' => 'Test-Wiki',
            'title' => 'Test Wiki',
            'format' => 'markdown',
        ];

        $params = ['with_content' => true];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/wikis', $params)
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showAll(1, $params));
    }

    #[Test]
    public function shouldUpdateWiki(): void
    {
        $expectedArray = [
            'slug' => 'Test-Wiki',
            'title' => 'Test Wiki',
            'format' => 'markdown',
            'content' => 'This is the test Wiki that has been updated',
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/wikis/Test-Wiki', ['content' => 'This is the test Wiki that has been updated'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->update(1, 'Test-Wiki', ['content' => 'This is the test Wiki that has been updated']));
    }

    #[Test]
    public function shouldRemoveWiki(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/wikis/Test-Wiki')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->remove(1, 'Test-Wiki'));
    }

    protected function getApiClass(): string
    {
        return Wiki::class;
    }
}
