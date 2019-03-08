<?php namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Badge
 *
 * @property-read string $link_url
 * @property-read string $image_url
 */
class Badge extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'link_url',
        'image_url'
    );

    /**
     * @param Client $client
     * @param Project $project
     * @param array  $data
     * @return Badge
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $badge = new static($project, $client);

        return $badge->hydrate($data);
    }

    /**
     * @param Project $project
     * @param Client $client
     */
    public function __construct(Project $project, Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
    }
}
