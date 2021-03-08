<?php

namespace App\Entity;

use App\Entity\Comment;
use App\Entity\Vote;
use App\Repository\NominationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=NominationRepository::class)
 * @ORM\Table(indexes={
 *      @ORM\Index(name="name_idx", columns={"name"}),
 *      @ORM\Index(name="percentage_idx", columns={"percentage"})
 * })
 */
class Nomination
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
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Member::class, inversedBy="nominations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $member;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pitch;

    /**
     * @ORM\OneToMany(targetEntity=Vote::class, mappedBy="nomination")
     */
    private $votes;

    /**
     * @ORM\Column(type="integer")
     */
    private $yes_count;

    /**
     * @ORM\Column(type="integer")
     */
    private $no_count;

    //45.23% is stored as 4523
    /**
     * @ORM\Column(type="integer")
     */
    private $percentage;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="nomination")
     */
    private $comments;

    public function __construct()
    {
        $this->votes = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getPitch(): ?string
    {
        return $this->pitch;
    }

    public function setPitch(?string $pitch): self
    {
        $this->pitch = $pitch;

        return $this;
    }

    /**
     * @return Collection|Vote[]
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setNomination($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getNomination() === $this) {
                $vote->setNomination(null);
            }
        }

        return $this;
    }

    public function getYesCount(): ?int
    {
        return $this->yes_count;
    }

    public function setYesCount(int $yes_count): self
    {
        $this->yes_count = $yes_count;

        return $this;
    }

    public function getNoCount(): ?int
    {
        return $this->no_count;
    }

    public function setNoCount(int $no_count): self
    {
        $this->no_count = $no_count;

        return $this;
    }

    public function setVoteCounts($voteRepository): self
    {
        $yesCount = $voteRepository->countYesVotesByNomination($this);
        $this->setYesCount(intval($yesCount[0]["count"]));

        $noCount = $voteRepository->countNoVotesByNomination($this);
        $this->setNoCount(intval($noCount[0]["count"]));

        return $this;
    }

    public function getPercentage(): ?int
    {
        return $this->percentage;
    }

    public function setPercentage(int $percentage): self
    {
        $this->percentage = $percentage;

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

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setNomination($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getNomination() === $this) {
                $comment->setNomination(null);
            }
        }

        return $this;
    }
}
