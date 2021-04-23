<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="bigint")
     */
    private $amount_cents;

    /**
     * @ORM\Column(type="bigint")
     */
    private $balance_cents;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAmountCents(): ?string
    {
        return $this->amount_cents;
    }

    public function setAmountCents(string $amount_cents): self
    {
        $this->amount_cents = $amount_cents;

        return $this;
    }

    public function getBalanceCents(): ?string
    {
        return $this->balance_cents;
    }

    public function setBalanceCents(string $balance_cents): self
    {
        $this->balance_cents = $balance_cents;

        return $this;
    }
}
