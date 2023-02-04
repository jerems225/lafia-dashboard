<?php

namespace App\Entity;

use App\Repository\StaticYearRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StaticYearRepository::class)]
class StaticYear
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $stats_year = null;

    #[ORM\Column]
    private ?bool $isSelect = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatsYear(): ?string
    {
        return $this->stats_year;
    }

    public function setStatsYear(string $stats_year): self
    {
        $this->stats_year = $stats_year;

        return $this;
    }

    public function isIsSelect(): ?bool
    {
        return $this->isSelect;
    }

    public function setIsSelect(bool $isSelect): self
    {
        $this->isSelect = $isSelect;

        return $this;
    }
}
