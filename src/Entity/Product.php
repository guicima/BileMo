<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Uid\Uuid;

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
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

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

    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"sensitive", "single_product"})
     */
    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"sensitive", "single_product"})
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hdd = null;

    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"sensitive", "single_product"})
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ram = null;

    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"sensitive", "single_product"})
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cpu = null;

    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"sensitive", "single_product"})
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $battery = null;

    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"sensitive", "single_product"})
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $connectivity = null;

    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"sensitive", "single_product"})
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $screen_size = null;

    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"sensitive", "single_product"})
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $screen_resolution = null;

    public function getId(): ?Uuid
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getHdd(): ?string
    {
        return $this->hdd;
    }

    public function setHdd(?string $hdd): self
    {
        $this->hdd = $hdd;

        return $this;
    }

    public function getRam(): ?string
    {
        return $this->ram;
    }

    public function setRam(?string $ram): self
    {
        $this->ram = $ram;

        return $this;
    }

    public function getCpu(): ?string
    {
        return $this->cpu;
    }

    public function setCpu(?string $cpu): self
    {
        $this->cpu = $cpu;

        return $this;
    }

    public function getBattery(): ?string
    {
        return $this->battery;
    }

    public function setBattery(?string $battery): self
    {
        $this->battery = $battery;

        return $this;
    }

    public function getConnectivity(): ?string
    {
        return $this->connectivity;
    }

    public function setConnectivity(?string $connectivity): self
    {
        $this->connectivity = $connectivity;

        return $this;
    }

    public function getScreenSize(): ?string
    {
        return $this->screen_size;
    }

    public function setScreenSize(?string $screen_size): self
    {
        $this->screen_size = $screen_size;

        return $this;
    }

    public function getScreenResolution(): ?string
    {
        return $this->screen_resolution;
    }

    public function setScreenResolution(?string $screen_resolution): self
    {
        $this->screen_resolution = $screen_resolution;

        return $this;
    }
}
