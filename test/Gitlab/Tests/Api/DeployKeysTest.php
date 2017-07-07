<?php namespace Gitlab\Tests\Api;

class DeployKeysTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetAllDeployKeys()
    {
        $expectedArray = $this->getMultipleDeployKeysData();

        $api = $this->getMultipleDeployKeysRequestMock('deploy_keys', $expectedArray);

        $this->assertEquals($expectedArray, $api->all());
    }

    protected function getMultipleDeployKeysRequestMock($path, $expectedArray = array(), $page = 1, $per_page = 20, $order_by = 'id', $sort = 'asc')
    {
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with($path, array('page' => $page, 'per_page' => $per_page, 'order_by' => $order_by, 'sort' => $sort))
            ->will($this->returnValue($expectedArray))
        ;

        return $api;
    }

    protected function getMultipleDeployKeysData()
    {
        return array(
            array(
                'id' => 1,
                'title' => 'Public key',
                'key' => 'ssh-rsa AAAAB3NzaC1yc2EAAAABJQAAAIEAiPWx6WM4lhHNedGfBpPJNPpZ7yKu+dnn1SJejgt4596k6YjzGGphH2TUxwKzxcKDKKezwkpfnxPkSMkuEspGRt/aZZ9wa++Oi7Qkr8prgHc4soW6NUlfDzpvZK2H5E7eQaSeP3SAwGmQKUFHCddNaP0L+hM7zhFNzjFvpaMgJw0=',
                'created_at' => '2013-10-02T10:12:29Z'
            ),
            array(
                'id' => 3,
                'title' => 'Another Public key',
                'key' => 'ssh-rsa AAAAB3NzaC1yc2EAAAABJQAAAIEAiPWx6WM4lhHNedGfBpPJNPpZ7yKu+dnn1SJejgt4596k6YjzGGphH2TUxwKzxcKDKKezwkpfnxPkSMkuEspGRt/aZZ9wa++Oi7Qkr8prgHc4soW6NUlfDzpvZK2H5E7eQaSeP3SAwGmQKUFHCddNaP0L+hM7zhFNzjFvpaMgJw0=',
                'created_at' => '2013-10-02T11:12:29Z'
            )
        );
    }

    protected function getApiClass()
    {
        return 'Gitlab\Api\DeployKeys';
    }
}
