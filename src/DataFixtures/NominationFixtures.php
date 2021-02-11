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

        $voteCounts = ["Foo" => ["Y" => 1, "N" => 1], "Bar" => ["Y" => 3, "N" => 1], "Baz" => ["Y" => 0, "N" => 3]];

        foreach (["Foo", "Bar", "Baz"] as $idx => $name) {
            $i = $idx + 1;
            $nomination = new Nomination();
            $nomination->setMember($member);
            $nomination->setName($name);

            $nomination->setYesCount($voteCounts[$name]["Y"]);
            $nomination->setNoCount($voteCounts[$name]["N"]);

            $now = new \DateTimeImmutable();
            $nomination->setCreatedAt($now);
            $nomination->setUpdatedAt($now);

            $this->addReference("nomination-$i", $nomination);

            $manager->persist($nomination);
        }
        $manager->flush();
    }
}
