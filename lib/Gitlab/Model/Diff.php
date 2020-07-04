<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Diff.
 *
 * @property-read string $old_path
 * @property-read string $new_path
 * @property-read string $a_mode
 * @property-read string $b_mode
 * @property-read string $diff
 * @property-read bool $new_file
 * @property-read bool $renamed_file
 * @property-read bool $deleted_file
 * @property-read Project $project
 */
class Diff extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = [
        'old_path',
        'new_path',
        'a_mode',
        'b_mode',
        'diff',
        'new_file',
        'renamed_file',
        'deleted_file',
        'project',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return Diff
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $diff = new static($project, $client);

        return $diff->hydrate($data);
    }

    /**
     * @param Project     $project
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Project $project, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->diff;
    }
}
