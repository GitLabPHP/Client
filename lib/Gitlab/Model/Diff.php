<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property string  $old_path
 * @property string  $new_path
 * @property string  $a_mode
 * @property string  $b_mode
 * @property string  $diff
 * @property bool    $new_file
 * @property bool    $renamed_file
 * @property bool    $deleted_file
 * @property Project $project
 */
class Diff extends AbstractModel
{
    /**
     * @var string[]
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
