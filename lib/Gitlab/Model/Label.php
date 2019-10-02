<?php namespace Gitlab\Model;

use Gitlab\Client;

/**
 * Class Label
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read string $color
 */
class Label extends AbstractModel
{
    /**
     * @var array
     */
    protected static $properties = array(
        'id',
        'name',
        'color'
    );

    /**
     * @param Client $client
     * @param Project $project
     * @param array  $data
     * @return Label
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $label = new static($project, $client);

        return $label->hydrate($data);
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
