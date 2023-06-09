<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 100, nullable: false)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(length: 20, nullable: false)]
    #[Assert\NotBlank]
    private string $nip;

    #[ORM\Column(length: 100, nullable: false)]
    #[Assert\NotBlank]
    private string $address;

    #[ORM\Column(length: 20, nullable: false)]
    #[Assert\NotBlank]
    private string $city;

    #[ORM\Column(length: 10, nullable: false)]
    #[Assert\NotBlank]
    private string $postalCode;

    #[ORM\OneToMany(targetEntity: Employee::class, mappedBy: "company", cascade: ["persist"], orphanRemoval: true)]
    private Collection $employees;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNip(): string
    {
        return $this->nip;
    }

    public function setNip(string $nip): self
    {
        $this->nip = $nip;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getEmployees(): Collection
    {
        return $this->employees;
    }

}
