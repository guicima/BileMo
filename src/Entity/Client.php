<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Uid\Uuid;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Hateoas\Relation("self", href = @Hateoas\Route(
 *          "app_client_show",
 *          parameters = { "id" = "expr(object.getId())" },
 *         absolute = true
 *      ))
 */
#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"client", "single_client"})
     */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    /** @Serializer\Exclude */
    #[ORM\ManyToOne(inversedBy: 'clients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"client_creation", "single_client", "sensitive"})
     */
    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $full_name = null;

    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"client_creation", "single_client", "client"})
     */
    #[Assert\NotBlank]
    #[Assert\Email(message: "The email {{ value }} is not a valid email.")]
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"single_client", "sensitive"})
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @Serializer\Expose()
     * @Serializer\Groups({"single_client", "sensitive"})
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->full_name;
    }

    public function setFullName(string $full_name): self
    {
        $this->full_name = $full_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
