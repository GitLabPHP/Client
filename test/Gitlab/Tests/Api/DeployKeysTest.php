<?php

namespace Gitlab\Tests\Api;

use Gitlab\Api\DeployKeys;

class DeployKeysTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllDeployKeys()
    {
        $expectedArray = $this->getMultipleDeployKeysData();

        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('deploy_keys', ['page' => 2, 'per_page' => 5])
            ->will($this->returnValue($expectedArray))
        ;

        $this->assertEquals($expectedArray, $api->all(['page' => 2, 'per_page' => 5]));
    }

    protected function getMultipleDeployKeysData()
    {
        return [
            [
                'id' => 1,
                'title' => 'Public key',
                'key' => 'ssh-rsa AAAAB3NzaC1yc2EAAAABJQAAAIEAiPWx6WM4lhHNedGfBpPJNPpZ7yKu+dnn1SJejgt4596k6YjzGGphH2TUxwKzxcKDKKezwkpfnxPkSMkuEspGRt/aZZ9wa++Oi7Qkr8prgHc4soW6NUlfDzpvZK2H5E7eQaSeP3SAwGmQKUFHCddNaP0L+hM7zhFNzjFvpaMgJw0=',
                'created_at' => '2013-10-02T10:12:29Z',
            ],
            [
                'id' => 3,
                'title' => 'Another Public key',
                'key' => 'ssh-rsa AAAAB3NzaC1yc2EAAAABJQAAAIEAiPWx6WM4lhHNedGfBpPJNPpZ7yKu+dnn1SJejgt4596k6YjzGGphH2TUxwKzxcKDKKezwkpfnxPkSMkuEspGRt/aZZ9wa++Oi7Qkr8prgHc4soW6NUlfDzpvZK2H5E7eQaSeP3SAwGmQKUFHCddNaP0L+hM7zhFNzjFvpaMgJw0=',
                'created_at' => '2013-10-02T11:12:29Z',
            ],
        ];
    }

    protected function getApiClass()
    {
        return DeployKeys::class;
    }
}
