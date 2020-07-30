<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property string  $file_path
 * @property string  $branch_name
 * @property Project $project
 */
class File extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'project',
        'file_path',
        'branch_name',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return File
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $file = new static($project, $data['file_path'], $client);

        return $file->hydrate($data);
    }

    /**
     * @param Project     $project
     * @param string|null $file_path
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Project $project, $file_path = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('file_path', $file_path);
    }
}
