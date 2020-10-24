<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @deprecated since version 10.1 and will be removed in 11.0.
 *
 * @property bool          $compare_timeout
 * @property bool          $compare_same_ref
 * @property Commit|null   $commit
 * @property Commit[]|null $commits
 * @property Diff[]|null   $diffs
 * @property Project       $project
 */
final class Comparison extends AbstractModel
{
    /**
     * @var string[]
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
        $file = new self($project, $client);

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
     * @param Project     $project
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Project $project, Client $client = null)
    {
        parent::__construct();
        $this->setClient($client);
        $this->setData('project', $project);
    }
}
