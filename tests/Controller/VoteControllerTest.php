<?php

namespace App\Tests\Controller;

use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VoteControllerTest extends WebTestCase
{
    //when not logged in
    //it should not let me vote(?)
    //when I vote Yes on a nomination
    //it should show on the page
    //when I vote No on a nomination
    //it should show on the page

    public function testVoteYes()
    {
        $client = static::createClient();

        $memberRepository = static::$container->get(MemberRepository::class);
        $member = $memberRepository->findOneByNickname('member-1');
        $client->loginUser($member);

        $listPage = $client->request('GET', '/nominations/charities');
        $this->assertResponseIsSuccessful();

        $listPage->filter('#nominations tbody tr:nth-child(1) td.yes-votes');
        $form = $listPage->selectButton('1')->form();
        $newPage = $client->submit($form);
        $this->assertResponseRedirects('/nominations/charities');

        $newPage->filter('#nominations tbody tr:nth-child(1) td.yes-votes');
        $form = $listPage->selectButton('2');
        $newPage->filter('#nominations tbody tr:nth-child(1) td.no-votes');
        $form = $listPage->selectButton('1');
    }
    public function testVoteNo()
    {
        $client = static::createClient();

        $memberRepository = static::$container->get(MemberRepository::class);
        $member = $memberRepository->findOneByNickname('member-1');
        $client->loginUser($member);

        $listPage = $client->request('GET', '/nominations/charities');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('#nominations tbody tr:nth-child(1) .name', 'Foo');
        $listPage->filter('#nominations tbody tr:nth-child(1) td.no-votes');
        $form = $listPage->selectButton('1')->form();
        $newPage = $client->submit($form);
        $this->assertResponseRedirects('/nominations/charities');

        $newPage->filter('#nominations tbody tr:nth-child(1) td.yes-votes');
        $form = $listPage->selectButton('1');
        $newPage->filter('#nominations tbody tr:nth-child(1) td.no-votes');
        $form = $listPage->selectButton('2');
    }

    public function testWithdrawYesVote()
    {
        $client = static::createClient();

        $memberRepository = static::$container->get(MemberRepository::class);
        $member = $memberRepository->findOneByNickname('member-1');
        $client->loginUser($member);

        $listPage = $client->request('GET', '/nominations/charities');
        $this->assertResponseIsSuccessful();

        $listPage->filter('#nominations tbody tr:nth-child(2) td.yes-votes');
        $form = $listPage->selectButton('4')->form();
        $newPage = $client->submit($form);
        $this->assertResponseRedirects('/nominations/charities');

        $newPage->filter('#nominations tbody tr:nth-child(2) td.yes-votes');
        $form = $listPage->selectButton('3');
        $newPage->filter('#nominations tbody tr:nth-child(2) td.no-votes');
        $form = $listPage->selectButton('0');
    }
    public function testWithdrawNoVote()
    {
        $client = static::createClient();

        $memberRepository = static::$container->get(MemberRepository::class);
        $member = $memberRepository->findOneByNickname('member-1');
        $client->loginUser($member);

        $listPage = $client->request('GET', '/nominations/charities');
        $this->assertResponseIsSuccessful();

        $listPage->filter('#nominations tbody tr:nth-child(3) td.no-votes');
        $form = $listPage->selectButton('4')->form();
        $newPage = $client->submit($form);
        $this->assertResponseRedirects('/nominations/charities');

        $newPage->filter('#nominations tbody tr:nth-child(3) td.yes-votes');
        $form = $listPage->selectButton('0');
        $newPage->filter('#nominations tbody tr:nth-child(3) td.no-votes');
        $form = $listPage->selectButton('3');
    }
    public function testChangeVoteFromYesToNo()
    {
        $client = static::createClient();

        $memberRepository = static::$container->get(MemberRepository::class);
        $member = $memberRepository->findOneByNickname('member-1');
        $client->loginUser($member);

        $listPage = $client->request('GET', '/nominations/charities');
        $this->assertResponseIsSuccessful();

        $listPage->filter('#nominations tbody tr:nth-child(2) td.yes-votes');
        $form = $listPage->selectButton('4')->form();
        $newPage = $client->submit($form);
        $this->assertResponseRedirects('/nominations/charities');

        $newPage->filter('#nominations tbody tr:nth-child(2) td.yes-votes');
        $form = $listPage->selectButton('3');
        $newPage->filter('#nominations tbody tr:nth-child(2) td.no-votes');
        $form = $listPage->selectButton('1');
    }
    public function testChangeVoteFromNoToYes()
    {
        $client = static::createClient();

        $memberRepository = static::$container->get(MemberRepository::class);
        $member = $memberRepository->findOneByNickname('member-1');
        $client->loginUser($member);

        $listPage = $client->request('GET', '/nominations/charities');
        $this->assertResponseIsSuccessful();

        $listPage->filter('#nominations tbody tr:nth-child(3) td.no-votes');
        $form = $listPage->selectButton('4')->form();
        $newPage = $client->submit($form);
        $this->assertResponseRedirects('/nominations/charities');

        $newPage->filter('#nominations tbody tr:nth-child(3) td.yes-votes');
        $form = $listPage->selectButton('1');
        $newPage->filter('#nominations tbody tr:nth-child(3) td.no-votes');
        $form = $listPage->selectButton('3');
    }
}
