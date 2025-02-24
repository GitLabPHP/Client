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

use Gitlab\Api\Jobs;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;

class JobsTest extends TestCase
{
    #[Test]
    public function shouldGetAllJobs(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A job'],
            ['id' => 2, 'name' => 'Another job'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/jobs', [
                'scope' => ['pending'],
            ])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->all(1, ['scope' => Jobs::SCOPE_PENDING]));
    }

    #[Test]
    public function shouldGetPipelineJobs(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A job'],
            ['id' => 2, 'name' => 'Another job'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipelines/2/jobs', [
                'scope' => ['pending', 'running'],
            ])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->pipelineJobs(1, 2, ['scope' => [Jobs::SCOPE_PENDING, Jobs::SCOPE_RUNNING]]));
    }

    #[Test]
    public function shouldGetPipelineJobsIncludingRetried(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A job'],
            ['id' => 2, 'name' => 'Another job'],
            ['id' => 3, 'name' => 'A job'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipelines/2/jobs', [
                'scope' => ['pending', 'running'],
                'include_retried' => true,
            ])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->pipelineJobs(1, 2, ['scope' => [Jobs::SCOPE_PENDING, Jobs::SCOPE_RUNNING], 'include_retried' => true]));
    }

    #[Test]
    public function shouldGetPipelineBridges(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A bridge job'],
            ['id' => 2, 'name' => 'Another bridge job'],
        ];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/pipelines/2/bridges', [
                'scope' => ['pending', 'running'],
            ])
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->pipelineBridges(1, 2, ['scope' => [Jobs::SCOPE_PENDING, Jobs::SCOPE_RUNNING]]));
    }

    #[Test]
    public function shouldGetJob(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A job'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/jobs/3')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->show(1, 3));
    }

    #[Test]
    public function shouldGetArtifacts(): void
    {
        $returnedStream = new Response(200, [], 'foobar');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('getAsResponse')
            ->with('projects/1/jobs/3/artifacts')
            ->willReturn($returnedStream);

        $this->assertEquals('foobar', $api->artifacts(1, 3)->getContents());
    }

    #[Test]
    public function shouldGetArtifactsByJobId(): void
    {
        $returnedStream = new Response(200, [], 'foobar');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('getAsResponse')
            ->with('projects/1/jobs/3/artifacts/artifact_path')
            ->willReturn($returnedStream);

        $this->assertEquals('foobar', $api->artifactByJobId(1, 3, 'artifact_path')->getContents());
    }

    #[Test]
    public function shouldGetArtifactsByRefName(): void
    {
        $returnedStream = new Response(200, [], 'foobar');

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('getAsResponse')
            ->with('projects/1/jobs/artifacts/master/download', [
                'job' => 'job name',
            ])
            ->willReturn($returnedStream);

        $this->assertEquals('foobar', $api->artifactsByRefName(1, 'master', 'job name')->getContents());
    }

    #[Test]
    public function shouldGetArtifactByRefName(): void
    {
        $returnedStream = new Response(200, [], 'foobar');
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('getAsResponse')
            ->with('projects/1/jobs/artifacts/master/raw/artifact_path', [
                'job' => 'job name',
            ])
            ->willReturn($returnedStream);

        $this->assertEquals('foobar', $api->artifactByRefName(1, 'master', 'job name', 'artifact_path')->getContents());
    }

    #[Test]
    public function shouldGetTrace(): void
    {
        $expectedString = 'some trace';

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('projects/1/jobs/3/trace')
            ->willReturn($expectedString);

        $this->assertEquals($expectedString, $api->trace(1, 3));
    }

    #[Test]
    public function shouldCancel(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A job'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/jobs/3/cancel')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->cancel(1, 3));
    }

    #[Test]
    public function shouldRetry(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A job'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/jobs/3/retry')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->retry(1, 3));
    }

    #[Test]
    public function shouldErase(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A job'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/jobs/3/erase')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->erase(1, 3));
    }

    #[Test]
    public function shouldKeepArtifacts(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A job'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/jobs/3/artifacts/keep')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->keepArtifacts(1, 3));
    }

    #[Test]
    public function shouldPlay(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A job'];

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('post')
            ->with('projects/1/jobs/3/play')
            ->willReturn($expectedArray)
        ;

        $this->assertEquals($expectedArray, $api->play(1, 3));
    }

    protected function getApiClass(): string
    {
        return Jobs::class;
    }
}
