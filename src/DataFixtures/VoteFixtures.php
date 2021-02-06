<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Vote;

class VoteFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $now = new \DateTimeImmutable();

        $votes_by_nomination = Array(
            "nomination-1" => Array(
                "member-2" => "N",
                "member-3" => "Y",
            ),
            "nomination-2" => Array(
                "member-1" => "Y",
                "member-2" => "Y",
                "member-3" => "Y",
                "member-4" => "Y",
            ),
            "nomination-3" => Array(
                "member-1" => "Y",
                "member-3" => "Y",
                "member-4" => "N",
            ),
        );

        foreach ($votes_by_nomination as $nomination_name => $votes)
        {
            $nomination = $this->getReference($nomination_name);

            foreach ($votes as $member_name => $value)
            {
                $vote = new Vote();
                $member = $this->getReference($member_name);
                $vote->setMember($member);
                $vote->setNomination($nomination);
                $vote->setValue($value);
                $vote->setCreatedAt($now);
                $manager->persist($vote);
            }
        }

        $manager->flush();
    }
}
