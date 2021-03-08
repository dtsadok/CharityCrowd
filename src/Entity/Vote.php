<?php

namespace App\Entity;

use App\Repository\VoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=VoteRepository::class)
 * @ORM\Table(
 *    name="vote",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="vote_unique", columns={"member_id", "nomination_id"})
 *    }
 * )
 */
class Vote
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Member::class, inversedBy="votes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity=Nomination::class, inversedBy="votes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $nomination;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): self
    {
        $this->member = $member;

        return $this;
    }

    public function getNomination(): ?Nomination
    {
        return $this->nomination;
    }

    public function setNomination(?Nomination $nomination): self
    {
        $this->nomination = $nomination;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getMonth(): string
    {
        return $this->getCreatedAt()->format('F');
    }

    public function getYear(): string
    {
        return $this->getCreatedAt()->format('Y');
    }


    public function isCurrent(): bool
    {
        $now = new \DateTimeImmutable();
        $currentMonth = $now->format('F');
        $currentYear = $now->format('Y');

        return $this->getMonth() == $currentMonth && $this->getYear() == $currentYear;
    }
}
