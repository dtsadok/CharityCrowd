<?php

namespace App\Service;

use App\Entity\Member;

class PercentagesCalculator
{
    public function setPercentages($nominations)
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
                //45.23% is stored as 4523
                $nomination->setPercentage(10000 * ($y-$n)/($totalYesVotes - $totalNoVotes));
            }
            else
            {
                $nomination->setPercentage(0);
            }
        }

        //print_r($nominations);

        return $nominations;
    }
}
