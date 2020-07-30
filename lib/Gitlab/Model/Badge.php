<?php

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @final
 *
 * @property int    $id
 * @property string $link_url
 * @property string $image_url
 * @property string $rendered_image_url
 * @property string $rendered_image_url
 * @property string $kind
 */
class Badge extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'link_url',
        'image_url',
        'rendered_link_url',
        'rendered_image_url',
        'kind',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return Badge
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $badge = new static($project, $client);

        return $badge->hydrate($data);
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
}
