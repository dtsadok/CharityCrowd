<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Vote;

class VoteFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $member1 = $this->getReference("member-1");
        $member2 = $this->getReference("member-2");
        $member3 = $this->getReference("member-3");
        $member4 = $this->getReference("member-4");
        $nomination1 = $this->getReference("nomination-1");
        $nomination2 = $this->getReference("nomination-2");
        $nomination3 = $this->getReference("nomination-3");
        $now = new \DateTimeImmutable();

        $vote = new Vote();
        $vote->setMember($member1);
        $vote->setNomination($nomination2);
        $vote->setValue('Y');
        $vote->setCreatedAt($now);
        $vote->setUpdatedAt($now);
        $manager->persist($vote);

        $vote = new Vote();
        $vote->setMember($member1);
        $vote->setNomination($nomination3);
        $vote->setValue('Y');
        $vote->setCreatedAt($now);
        $vote->setUpdatedAt($now);
        $manager->persist($vote);

        $vote = new Vote();
        $vote->setMember($member2);
        $vote->setNomination($nomination1);
        $vote->setValue('N');
        $vote->setCreatedAt($now);
        $vote->setUpdatedAt($now);
        $manager->persist($vote);

        $vote = new Vote();
        $vote->setMember($member3);
        $vote->setNomination($nomination1);
        $vote->setValue('Y');
        $vote->setCreatedAt($now);
        $vote->setUpdatedAt($now);
        $manager->persist($vote);

        $manager->flush();
    }
}
