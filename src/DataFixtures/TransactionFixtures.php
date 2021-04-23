<?php

namespace App\DataFixtures;

use App\Entity\Transaction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TransactionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $transaction = new Transaction();
        $transaction->setType("deposit");
        $transaction->setAmountCents(100000);
        $transaction->setBalanceCents(100000);
        $manager->persist($transaction);

        $manager->flush();
    }
}
