<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

class SnippetsTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllSnippets()
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'A snippet'],
            ['id' => 2, 'title' => 'Another snippet'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/snippets')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(1));
    }

    /**
     * @test
     */
    public function shouldShowSnippet()
    {
        $expectedArray = ['id' => 2, 'title' => 'Another snippet'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/snippets/2')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->show(1, 2));
    }

    /**
     * @test
     */
    public function shouldCreateSnippet()
    {
        $expectedArray = ['id' => 3, 'title' => 'A new snippet'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/snippets', ['title' => 'A new snippet', 'code' => 'A file', 'file_name' => 'file.txt', 'visibility' => 'public'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create(1, 'A new snippet', 'file.txt', 'A file', 'public'));
    }

    /**
     * @test
     */
    public function shouldUpdateSnippet()
    {
        $expectedArray = ['id' => 3, 'title' => 'Updated snippet'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/snippets/3', ['title' => 'Updated snippet', 'code' => 'New content', 'file_name' => 'new_file.txt'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(1, 3, ['file_name' => 'new_file.txt', 'code' => 'New content', 'title' => 'Updated snippet']));
    }

    /**
     * @test
     */
    public function shouldShowContent()
    {
        $expectedString = 'New content';

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/snippets/3/raw')
            ->will($this->returnValue($expectedString))
        ;

        $this->assertEquals($expectedString, $api->content(1, 3));
    }

    /**
     * @test
     */
    public function shouldRemoveSnippet()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/snippets/3')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->remove(1, 3));
    }

    /**
     * @test
     */
    public function shouldGetNotes()
    {
        $expectedArray = [
            ['id' => 1, 'body' => 'A note'],
            ['id' => 2, 'body' => 'Another note'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/snippets/2/notes')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->showNotes(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetNote()
    {
        $expectedArray = ['id' => 3, 'body' => 'A new note'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/snippets/2/notes/3')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->showNote(1, 2, 3));
    }

    /**
     * @test
     */
    public function shouldCreateNote()
    {
        $expectedArray = ['id' => 3, 'body' => 'A new note'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/snippets/2/notes', ['body' => 'A new note'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->addNote(1, 2, 'A new note'));
    }

    /**
     * @test
     */
    public function shouldUpdateNote()
    {
        $expectedArray = ['id' => 3, 'body' => 'An edited comment'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/snippets/2/notes/3', ['body' => 'An edited comment'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateNote(1, 2, 3, 'An edited comment'));
    }

    /**
     * @test
     */
    public function shouldRemoveNote()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/snippets/2/notes/3')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->removeNote(1, 2, 3));
    }

    /**
     * @test
     */
    public function shouldIssueSnippetAwardEmoji()
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'sparkles'],
            ['id' => 2, 'name' => 'heart_eyes'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/snippets/2/award_emoji')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->awardEmoji(1, 2));
    }

    /**
     * @test
     */
    public function shouldRevokeSnippetAwardEmoji()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/snippets/2/award_emoji/3')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals(true, $api->removeAwardEmoji(1, 2, 3));
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\Snippets';
    }
}
