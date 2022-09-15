<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Hateoas\Relation("self", href = @Hateoas\Route(
 *          "show_product",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      ))
 * @Serializer\ExclusionPolicy("all")
 */
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"product", "single_product"})
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"product", "single_product"})
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"sensitive", "single_product"})
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $description;

    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"sensitive", "single_product"})
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }
}
