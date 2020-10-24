<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @deprecated since version 10.1 and will be removed in 11.0.
 *
 * @property int    $id
 * @property string $name
 * @property string $color
 */
final class Label extends AbstractModel
{
    /**
     * @var string[]
     */
    protected static $properties = [
        'id',
        'name',
        'color',
    ];

    /**
     * @param Client  $client
     * @param Project $project
     * @param array   $data
     *
     * @return Label
     */
    public static function fromArray(Client $client, Project $project, array $data)
    {
        $label = new self($project, $client);

        return $label->hydrate($data);
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
