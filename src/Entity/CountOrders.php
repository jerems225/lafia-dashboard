<?php

namespace App\Entity;

use App\Repository\CountOrdersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountOrdersRepository::class)]
class CountOrders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $check_at = null;

    #[ORM\Column]
    private ?int $orders = null;

    #[ORM\Column]
    private ?float $percent = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column]
    private ?float $deliveries_amount = null;

    #[ORM\Column]
    private ?float $deliveries_percent = null;

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

    public function getOrders(): ?int
    {
        return $this->orders;
    }

    public function setOrders(int $orders): self
    {
        $this->orders = $orders;

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

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDeliveriesAmount(): ?float
    {
        return $this->deliveries_amount;
    }

    public function setDeliveriesAmount(float $deliveries_amount): self
    {
        $this->deliveries_amount = $deliveries_amount;

        return $this;
    }

    public function getDeliveriesPercent(): ?float
    {
        return $this->deliveries_percent;
    }

    public function setDeliveriesPercent(float $deliveries_percent): self
    {
        $this->deliveries_percent = $deliveries_percent;

        return $this;
    }
}
