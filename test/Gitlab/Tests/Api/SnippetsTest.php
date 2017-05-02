<?php namespace Gitlab\Tests\Api;

class SnippetsTest extends ApiTestCase
{
    /**
     * @test
     */
    public function shouldGetAllSnippets()
    {
        $expectedArray = array(
            array('id' => 1, 'title' => 'A snippet'),
            array('id' => 2, 'title' => 'Another snippet'),
        );

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
        $expectedArray = array('id' => 2, 'title' => 'Another snippet');

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
        $expectedArray = array('id' => 3, 'title' => 'A new snippet');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/snippets', array('title' => 'A new snippet', 'code' => 'A file', 'file_name' => 'file.txt', 'visibility_level' => 0))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->create(1, 'A new snippet', 'file.txt', 'A file', 0));
    }

    /**
     * @test
     */
    public function shouldUpdateSnippet()
    {
        $expectedArray = array('id' => 3, 'title' => 'Updated snippet');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/snippets/3', array('title' => 'Updated snippet', 'code' => 'New content', 'file_name' => 'new_file.txt'))
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(1, 3, array('file_name' => 'new_file.txt', 'code' => 'New content', 'title' => 'Updated snippet')));
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

    protected function getApiClass()
    {
        return 'Gitlab\Api\Snippets';
    }
}
