<?php

namespace App\Tests\Controller;

use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHome()
    {
        $client = static::createClient();

        //$memberRepository = static::$container->get(MemberRepository::class);
        //$member = $memberRepository->findOneByNickname('member-1');
        //$client->loginUser($member);

        $homePage = $client->request('GET', '/nominations/charities');
        $this->assertResponseIsSuccessful();
    }
}
