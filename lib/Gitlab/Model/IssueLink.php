<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property-read int $issue_link_id
 * @property-read Issue $issue
 */
class IssueLink extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'issue_link_id',
        'issue',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return IssueLink
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $issue = Issue::fromArray($client, $project, $data);
        $issueLink = new static($issue, $data['issue_link_id'], $client);

        return $issueLink->hydrate($data);
    }

    /**
     * @param Issue       $issue
     * @param int|null    $issue_link_id
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Issue $issue, $issue_link_id = null, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('issue', $issue);
        $this->setData('issue_link_id', $issue_link_id);
    }
}
