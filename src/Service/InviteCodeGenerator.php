<?php

namespace App\Service;

use App\Entity\InviteCode;

class InviteCodeGenerator
{
    public function makeCode($codeLength) : string
    {
        $code = "";
        for ($i=0; $i<$codeLength; $i++)
        {
            $letters = range('A', 'Z');
            $code .= $letters[array_rand($letters)];
        }

        return $code;
    }

    public function makeInviteCodes($count, $codeLength, $entityManager)
    {
        $inviteCodes = [];
        for ($i=0; $i<$count; $i++)
        {
            $code = $this->makeCode($codeLength);
            $inviteCodes[$i] = new InviteCode();
            $inviteCodes[$i]->setCode($code);
            $inviteCodes[$i]->setActive(true);
            $entityManager->persist($inviteCodes[$i]);
        }

        $entityManager->flush();

        return $inviteCodes;
    }
}
