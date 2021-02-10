<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private ?User $user = null;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="tasks")
     */
    private ?Customer $customer = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $label = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $closed = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $closedAt = null;

    public function __construct() {
        $this->createdAt = new \DateTime();
    }

    public function __toString(): string {
        return $this->getLabel();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }

    public function getClosedAt(): ?\DateTimeInterface
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeInterface $closedAt): self
    {
        $this->closedAt = $closedAt;

        return $this;
    }
}
