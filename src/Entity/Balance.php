<?php

namespace App\Entity;

use App\Repository\BalanceRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=BalanceRepository::class)
 * @ORM\Table(indexes={
 *      @ORM\Index(name="amount_cents_idx", columns={"amount_cents"}),
 * })
 */
class Balance
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="bigint")
     */
    private $amount_cents;

    public function getId(): ?int
    {
        return $this->id;
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
}
