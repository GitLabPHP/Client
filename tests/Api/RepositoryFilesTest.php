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

use Gitlab\Api\RepositoryFiles;

class RepositoryFilesTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetBlob(): void
    {
        $expectedString = 'something in a file';

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/files/dir%2Ffile1.txt/raw', ['ref' => 'abcd1234'])
            ->will($this->returnValue($expectedString))
        ;

        $this->assertEquals($expectedString, $api->getRawFile(1, 'dir/file1.txt', 'abcd1234'));
    }

    /**
     * @test
     */
    public function shouldGetFile(): void
    {
        $expectedArray = ['file_name' => 'file1.txt', 'file_path' => 'dir/file1.txt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/repository/files/dir%2Ffile1.txt', ['ref' => 'abcd1234'])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->getFile(1, 'dir/file1.txt', 'abcd1234'));
    }

    /**
     * @test
     */
    public function shouldCreateFile(): void
    {
        $expectedArray = ['file_name' => 'file1.txt', 'file_path' => 'dir/file1.txt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/files/dir%2Ffile1.txt', [
                'file_path' => 'dir/file1.txt',
                'branch' => 'master',
                'content' => 'some contents',
                'commit_message' => 'Added new file',
            ])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createFile(1, [
            'file_path' => 'dir/file1.txt',
            'content' => 'some contents',
            'branch' => 'master',
            'commit_message' => 'Added new file',
        ]));
    }

    /**
     * @test
     */
    public function shouldCreateFileWithEncoding(): void
    {
        $expectedArray = ['file_name' => 'file1.txt', 'file_path' => 'dir/file1.txt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/files/dir%2Ffile1.txt', [
                'file_path' => 'dir/file1.txt',
                'branch' => 'master',
                'encoding' => 'text',
                'content' => 'some contents',
                'commit_message' => 'Added new file',
            ])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createFile(1, [
            'file_path' => 'dir/file1.txt',
            'content' => 'some contents',
            'branch' => 'master',
            'commit_message' => 'Added new file',
            'encoding' => 'text',
        ]));
    }

    /**
     * @test
     */
    public function shouldCreateFileWithAuthor(): void
    {
        $expectedArray = ['file_name' => 'file1.txt', 'file_path' => 'dir/file1.txt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/repository/files/dir%2Ffile1.txt', [
                'file_path' => 'dir/file1.txt',
                'branch' => 'master',
                'content' => 'some contents',
                'commit_message' => 'Added new file',
                'author_email' => 'gitlab@example.com',
                'author_name' => 'GitLab User',
            ])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->createFile(1, [
            'file_path' => 'dir/file1.txt',
            'content' => 'some contents',
            'branch' => 'master',
            'commit_message' => 'Added new file',
            'author_email' => 'gitlab@example.com',
            'author_name' => 'GitLab User',
        ]));
    }

    /**
     * @test
     */
    public function shouldUpdateFile(): void
    {
        $expectedArray = ['file_name' => 'file1.txt', 'file_path' => 'dir/file1.txt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/repository/files/dir%2Ffile1.txt', [
                'file_path' => 'dir/file1.txt',
                'branch' => 'master',
                'content' => 'some new contents',
                'commit_message' => 'Updated new file',
            ])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateFile(1, [
            'file_path' => 'dir/file1.txt',
            'content' => 'some new contents',
            'branch' => 'master',
            'commit_message' => 'Updated new file',
        ]));
    }

    /**
     * @test
     */
    public function shouldUpdateFileWithEncoding(): void
    {
        $expectedArray = ['file_name' => 'file1.txt', 'file_path' => 'dir/file1.txt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/repository/files/dir%2Ffile1.txt', [
                'file_path' => 'dir/file1.txt',
                'branch' => 'master',
                'encoding' => 'base64',
                'content' => 'c29tZSBuZXcgY29udGVudHM=',
                'commit_message' => 'Updated file',
            ])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateFile(1, [
            'file_path' => 'dir/file1.txt',
            'content' => 'c29tZSBuZXcgY29udGVudHM=',
            'branch' => 'master',
            'commit_message' => 'Updated file',
            'encoding' => 'base64',
        ]));
    }

    /**
     * @test
     */
    public function shouldUpdateFileWithAuthor(): void
    {
        $expectedArray = ['file_name' => 'file1.txt', 'file_path' => 'dir/file1.txt'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('put')
            ->with('projects/1/repository/files/dir%2Ffile1.txt', [
                'file_path' => 'dir/file1.txt',
                'branch' => 'master',
                'content' => 'some new contents',
                'commit_message' => 'Updated file',
                'author_email' => 'gitlab@example.com',
                'author_name' => 'GitLab User',
            ])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->updateFile(1, [
            'file_path' => 'dir/file1.txt',
            'content' => 'some new contents',
            'branch' => 'master',
            'commit_message' => 'Updated file',
            'author_email' => 'gitlab@example.com',
            'author_name' => 'GitLab User',
        ]));
    }

    /**
     * @test
     */
    public function shouldDeleteFile(): void
    {
        $expectedArray = ['file_name' => 'app/project.rb', 'branch' => 'master'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/repository/files/dir%2Ffile1.txt', [
                'file_path' => 'dir/file1.txt',
                'branch' => 'master',
                'commit_message' => 'Deleted file',
            ])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->deleteFile(1, [
            'file_path' => 'dir/file1.txt',
            'branch' => 'master',
            'commit_message' => 'Deleted file',
        ]));
    }

    /**
     * @test
     */
    public function shouldDeleteFileWithAuthor(): void
    {
        $expectedArray = ['file_name' => 'app/project.rb', 'branch' => 'master'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('delete')
            ->with('projects/1/repository/files/dir%2Ffile1.txt', [
                'file_path' => 'dir/file1.txt',
                'branch' => 'master',
                'commit_message' => 'Deleted file',
                'author_email' => 'gitlab@example.com',
                'author_name' => 'GitLab User',
            ])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->deleteFile(1, [
            'file_path' => 'dir/file1.txt',
            'branch' => 'master',
            'commit_message' => 'Deleted file',
            'author_email' => 'gitlab@example.com',
            'author_name' => 'GitLab User',
        ]));
    }

    /**
     * @test
     */
    public function shouldFileExists(): void
    {
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('head')
            ->with('projects/1/repository/files/dir%2Ffile1.txt', [
                'ref' => 'master',
            ])
            ->will($this->returnValue(200))
        ;

        $this->assertTrue($api->fileExists(1, 'dir/file1.txt', 'master'));
    }

    /**
     * @return string
     */
    protected function getApiClass()
    {
        return RepositoryFiles::class;
    }
}
