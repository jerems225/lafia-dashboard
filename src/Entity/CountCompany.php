<?php

namespace App\Entity;

use App\Repository\CountCompanyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountCompanyRepository::class)]
class CountCompany
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $check_at = null;

    #[ORM\Column]
    private ?int $companies = null;

    #[ORM\Column]
    private ?float $percent = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCheckAt(): ?\DateTimeImmutable
    {
        return $this->check_at;
    }

    public function setCheckAt(\DateTimeImmutable $check_at): self
    {
        $this->check_at = $check_at;

        return $this;
    }

    public function getCompanies(): ?int
    {
        return $this->companies;
    }

    public function setCompanies(int $companies): self
    {
        $this->companies = $companies;

        return $this;
    }

    public function getPercent(): ?float
    {
        return $this->percent;
    }

    public function setPercent(float $percent): self
    {
        $this->percent = $percent;

        return $this;
    }
}
