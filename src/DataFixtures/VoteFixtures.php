<?php

namespace App\DataFixtures;

use App\Entity\Vote;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VoteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
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
                "member-1" => "N",
                "member-2" => "N",
                "member-3" => "N",
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
                $now = new \DateTimeImmutable();
                $vote->setCreatedAt($now);
                $manager->persist($vote);
            }
        }

        $manager->flush();
    }
    
    public function getDependencies()
    {
        return [
            MemberFixtures::class,
            NominationFixtures::class
        ];
    }
}
