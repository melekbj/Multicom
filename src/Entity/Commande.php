<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $designation = null;

    #[ORM\Column]
    private ?int $qte = null;

    #[ORM\Column]
    private ?float $puht = null;

    #[ORM\Column(nullable: true)]
    private ?float $ttva = null;

    #[ORM\Column(nullable: true)]
    private ?float $remise = null;

    #[ORM\Column(nullable: true)]
    private ?float $timbre = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?Clients $client = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    public function getQte(): ?int
    {
        return $this->qte;
    }

    public function setQte(int $qte): self
    {
        $this->qte = $qte;

        return $this;
    }

    public function getPuht(): ?float
    {
        return $this->puht;
    }

    public function setPuht(float $puht): self
    {
        $this->puht = $puht;

        return $this;
    }

    public function getTtva(): ?float
    {
        return $this->ttva;
    }

    public function setTtva(?float $ttva): self
    {
        $this->ttva = $ttva;

        return $this;
    }

    public function getRemise(): ?float
    {
        return $this->remise;
    }

    public function setRemise(?float $remise): self
    {
        $this->remise = $remise;

        return $this;
    }

    public function getTimbre(): ?float
    {
        return $this->timbre;
    }

    public function setTimbre(?float $timbre): self
    {
        $this->timbre = $timbre;

        return $this;
    }

    public function getClient(): ?Clients
    {
        return $this->client;
    }

    public function setClient(?Clients $client): self
    {
        $this->client = $client;

        return $this;
    }
}
