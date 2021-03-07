<?php

namespace App\DataFixtures;

use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MemberFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        for ($i=1; $i<=4 ; $i++) {
            $nickname = "member-$i";
            $member = new Member();
            $member->setNickname($nickname);
            $member->setPassword(
                $this->passwordEncoder->encodePassword($member, '1234')
            );
            $now = new \DateTimeImmutable();
            $member->setCreatedAt($now);
            $member->setUpdatedAt($now);
            $manager->persist($member);

            $this->addReference($nickname, $member);
        }

        $manager->flush();
    }
}
