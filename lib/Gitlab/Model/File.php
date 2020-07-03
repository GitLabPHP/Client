<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class File
 *
 * @property-read string $file_path
 * @property-read string $branch_name
 * @property-read Project $project
 */
class File extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'project',
        'file_path',
        'branch_name'
    );

    /**
     * @param Client $client
     * @param Project $project
     * @param array $data
     * @return File
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $file = new self($project, $data['file_path'], $client);

        return $file->hydrate($data);
    }

    /**
     * @param Project $project
     * @param string $file_path
     * @param Client $client
     */
    public function __construct(Project $project, $file_path = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('file_path', $file_path);
    }
}
