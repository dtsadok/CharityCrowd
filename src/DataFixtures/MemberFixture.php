<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Member;

class MemberFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $member = new Member();
        $member->setNickname("daniel");
        $now = new \DateTimeImmutable();
        $member->setCreatedAt($now);
        $member->setUpdatedAt($now);

        $manager->persist($member);
        $manager->flush();
    }
}
