<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Comparison.
 *
 * @property-read bool $compare_timeout
 * @property-read bool $compare_same_ref
 * @property-read Commit $commit
 * @property-read Commit[] $commits
 * @property-read Diff[] $diffs
 * @property-read Project $project
 */
class Comparison extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = [
        'commit',
        'commits',
        'diffs',
        'compare_timeout',
        'compare_same_ref',
        'project',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return Comparison
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $file = new static($project, $client);

        if (isset($data['commit'])) {
            $data['commit'] = Commit::fromArray($client, $project, $data['commit']);
        }

        if (isset($data['commits'])) {
            $commits = [];
            foreach ($data['commits'] as $commit) {
                $commits[] = Commit::fromArray($client, $project, $commit);
            }

            $data['commits'] = $commits;
        }

        if (isset($data['diffs'])) {
            $diffs = [];
            foreach ($data['diffs'] as $diff) {
                $diffs[] = Diff::fromArray($client, $project, $diff);
            }

            $data['diffs'] = $diffs;
        }

        return $file->hydrate($data);
    }

    /**
     * @param Project $project
     * @param Client  $client
     */
    public function __construct(Project $project, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
    }
}
