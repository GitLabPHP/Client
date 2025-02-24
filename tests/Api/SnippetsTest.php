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

use Gitlab\Api\Snippets;
use PHPUnit\Framework\Attributes\Test;

class SnippetsTest extends TestCase
{
    #[Test]
    public function shouldGetAllSnippets(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'A snippet'],
            ['id' => 2, 'title' => 'Another snippet'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/snippets')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(1));
    }

    #[Test]
    public function shouldShowSnippet(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'Another snippet'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/snippets/2')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    #[Test]
    public function shouldCreateSnippet(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'A new snippet'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/snippets', ['title' => 'A new snippet', 'code' => 'A file', 'file_name' => 'file.txt', 'visibility' => 'public'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->create(1, 'A new snippet', 'file.txt', 'A file', 'public'));
    }

    #[Test]
    public function shouldUpdateSnippet(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'Updated snippet'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/snippets/3', ['title' => 'Updated snippet', 'code' => 'New content', 'file_name' => 'new_file.txt'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->update(1, 3, ['file_name' => 'new_file.txt', 'code' => 'New content', 'title' => 'Updated snippet']));
    }

    #[Test]
    public function shouldShowContent(): void
    {
        $expectedString = 'New content';

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/snippets/3/raw')
            ->willReturn($expectedString);

        $this->assertEquals($expectedString, $api->content(1, 3));
    }

    #[Test]
    public function shouldRemoveSnippet(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/snippets/3')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->remove(1, 3));
    }

    #[Test]
    public function shouldGetNotes(): void
    {
        $expectedArray = [
            ['id' => 1, 'body' => 'A note'],
            ['id' => 2, 'body' => 'Another note'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/snippets/2/notes')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showNotes(1, 2));
    }

    #[Test]
    public function shouldGetNote(): void
    {
        $expectedArray = ['id' => 3, 'body' => 'A new note'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/snippets/2/notes/3')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->showNote(1, 2, 3));
    }

    #[Test]
    public function shouldCreateNote(): void
    {
        $expectedArray = ['id' => 3, 'body' => 'A new note'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/snippets/2/notes', ['body' => 'A new note'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->addNote(1, 2, 'A new note'));
    }

    #[Test]
    public function shouldUpdateNote(): void
    {
        $expectedArray = ['id' => 3, 'body' => 'An edited comment'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/snippets/2/notes/3', ['body' => 'An edited comment'])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->updateNote(1, 2, 3, 'An edited comment'));
    }

    #[Test]
    public function shouldRemoveNote(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/snippets/2/notes/3')
            ->willReturn($expectedBool);

        $this->assertEquals($expectedBool, $api->removeNote(1, 2, 3));
    }

    #[Test]
    public function shouldIssueSnippetAwardEmoji(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'sparkles'],
            ['id' => 2, 'name' => 'heart_eyes'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/snippets/2/award_emoji')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->awardEmoji(1, 2));
    }

    #[Test]
    public function shouldRevokeSnippetAwardEmoji(): void
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/snippets/2/award_emoji/3')
            ->willReturn($expectedBool);

        $this->assertEquals(true, $api->removeAwardEmoji(1, 2, 3));
    }

    protected function getApiClass(): string
    {
        return Snippets::class;
    }
}
