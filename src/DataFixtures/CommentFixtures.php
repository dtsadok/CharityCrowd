<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $member = $this->getReference('member-2');
        $nomination = $this->getReference('nomination-3');

        $comment = new Comment();
        $comment->setMember($member);
        $comment->setNomination($nomination);
        $comment->setCommentText("What a great charity!");
        $manager->persist($comment);
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
