<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Member;

class MemberFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i=1; $i<=4 ; $i++) {
            $nickname = "member-$i";
            $member = new Member();
            $member->setNickname($nickname);
            $now = new \DateTimeImmutable();
            $member->setCreatedAt($now);
            $member->setUpdatedAt($now);
            $manager->persist($member);

            $this->addReference($nickname, $member);
        }

        $manager->flush();
    }
}
