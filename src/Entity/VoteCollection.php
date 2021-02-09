<?php

namespace App\Entity;

//This Entity is used to help render VoteButtons
class VoteCollection
{
    private $nomination_id;
    private $value; //Y or N
    private $count;

    public function __construct(?int $nomination_id, ?string $value, ?int $count)
    {
        $this->nomination_id = $nomination_id;
        $this->value = $value;
        $this->count = $count;
    }

    public function getNominationId(): ?int
    {
        return $this->nomination_id;
    }

    public function setNominationId(?int $nomination_id): self
    {
        $this->nomination_id = $nomination_id;

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

    public function getCount(): ?int
    {
        return $this->Count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }
}
