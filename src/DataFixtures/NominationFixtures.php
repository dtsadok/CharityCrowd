<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Nomination;

class NominationFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $member = $this->getReference("member-1");

        $voteCounts = ["Foo" => ["Y" => 1, "N" => 1], "Bar" => ["Y" => 4, "N" => 0], "Baz" => ["Y" => 0, "N" => 4]];

        $now = new \DateTimeImmutable();

        foreach (["Foo", "Bar", "Baz"] as $idx => $name) {
            $i = $idx + 1;
            $nomination = new Nomination();
            $nomination->setMember($member);
            $nomination->setName($name);

            $nomination->setYesCount($voteCounts[$name]["Y"]);
            $nomination->setNoCount($voteCounts[$name]["N"]);

            $nomination->setCreatedAt($now);
            $nomination->setUpdatedAt($now);

            $this->addReference("nomination-$i", $nomination);

            $manager->persist($nomination);
        }

        $oneMonth = new \DateInterval('P1M');
        $lastMonth = $now->sub($oneMonth);
        $nomination = new Nomination();
        $nomination->setMember($member);
        $nomination->setName("Old Nomination");
        $nomination->setYesCount(0);
        $nomination->setNoCount(0);
        $nomination->setCreatedAt($lastMonth);
        $nomination->setUpdatedAt($lastMonth);
        $this->addReference("nomination-4", $nomination);
        $manager->persist($nomination);

        $manager->flush();
    }
}
