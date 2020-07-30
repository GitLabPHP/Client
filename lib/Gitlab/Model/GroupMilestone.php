<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property int    $id
 * @property int    $iid
 * @property Group  $group
 * @property int    $group_id
 * @property string $title
 * @property string $description
 * @property string $state
 * @property string $created_at
 * @property string $updated_at
 * @property string $due_date
 * @property string $start_date
 */
class GroupMilestone extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'iid',
        'group',
        'group_id',
        'title',
        'description',
        'state',
        'created_at',
        'updated_at',
        'due_date',
        'start_date',
    ];

    /**
     * @param Client $client
     * @param Group  $group
     * @param array  $data
     *
     * @return GroupMilestone
     */
    public static function fromArray(Client $client, Group $group, array $data)
    {
        $milestone = new static($group, $data['id'], $client);

        return $milestone->hydrate($data);
    }

    /**
     * GroupMilestone constructor.
     *
     * @param Group       $group
     * @param int         $id
     * @param Client|null $client
     *
     * @return void
     */
    public function __construct(Group $group, $id, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('id', $id);
        $this->setData('group', $group);
    }
}
