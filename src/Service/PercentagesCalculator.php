<?php

namespace App\Service;

use App\Entity\Member;

class PercentagesCalculator
{
    public function setPercentages($nominations, $entityManager)
    {
        $totalYesVotes = 0;
        $totalNoVotes = 0;

        foreach ($nominations as $nomination)
        {
            $y = $nomination->getYesCount();
            $n = $nomination->getNoCount();

            if ($y > $n)
            {
                $totalYesVotes += $y;
                $totalNoVotes += $n;
            }
        }

        //pass 2
        foreach ($nominations as $nomination) {
            $id = $nomination->getId();

            $y = $nomination->getYesCount();
            $n = $nomination->getNoCount();

            if ($y > $n)
            {
                //45.23% is stored as 0.4523
                $nomination->setPercentage(($y-$n)/floatval($totalYesVotes - $totalNoVotes));
            }
            else
            {
                $nomination->setPercentage(0);
            }

            $entityManager->persist($nomination);
        }

        return $nominations;
    }
}
