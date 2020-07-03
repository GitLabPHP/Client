<?php

namespace Gitlab\Tests\Api;

class WikiTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCreateWiki()
    {
        $expectedArray = [
            "format" => "markdown",
            "slug" => "Test-Wiki",
            "title" => "Test Wiki",
            "content" => "This is the test Wiki",
        ];


        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/wikis', [
                "format" => "markdown",
                "title" => "Test Wiki",
                "content" => "This is the test Wiki"
            ])
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->create(
            1,
            [
                "format" => "markdown",
                "title" => "Test Wiki",
                "content" => "This is the test Wiki"
            ]
        ));
    }

    /**
     * @test
     */
    public function shouldShowWiki()
    {
        $expectedArray = [
            "slug" => "Test-Wiki",
            "title" => "Test Wiki",
            "format" => "markdown"
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/wikis/Test-Wiki')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->show(1, "Test-Wiki"));
    }

    /**
     * @test
     */
    public function shouldShowAllWiki()
    {
        $expectedArray = [
            "slug" => "Test-Wiki",
            "title" => "Test Wiki",
            "format" => "markdown"
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/wikis')
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->showAll(1));
    }

    /**
     * @test
     */
    public function shouldUpdateWiki()
    {
        $expectedArray = [
            'slug' => 'Test-Wiki',
            'title' => 'Test Wiki',
            "format" => "markdown",
            "content" => "This is the test Wiki that has been updated"
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/wikis/Test-Wiki', ["content" => "This is the test Wiki that has been updated"])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->update(1, "Test-Wiki", ["content" => "This is the test Wiki that has been updated"]));
    }

    /**
     * @test
     */
    public function shouldRemoveWiki()
    {
        $expectedBool = true;

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/wikis/Test-Wiki')
            ->will($this->returnValue($expectedBool))
        ;

        $this->assertEquals($expectedBool, $api->remove(1, "Test-Wiki"));
    }


    protected function getApiClass()
    {
        return 'Gitlab\Api\Wiki';
    }
}
