<?php

declare(strict_types=1);

namespace App\Representation;

use Hateoas\Configuration\Annotation as Hateoas;
// use JMS\Serializer\Annotation as Serializer;

/**
 *
 * @Hateoas\Relation(
 *     "items",
 *     embedded = @Hateoas\Embedded("expr(object.getResources())")
 * )
 */
class RestrainedCollectionRepresentation
{
    /**
     * @var mixed
     */
    private $resources;

    /**
     * @param array|\Traversable $resources
     */
    public function __construct($resources)
    {
        if ($resources instanceof \Traversable) {
            $resources = iterator_to_array($resources);
        }

        $this->resources = $resources;
    }

    /**
     * @return mixed
     */
    public function getResources()
    {
        return $this->resources;
    }
}
