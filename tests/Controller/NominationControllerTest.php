<?php

namespace App\Tests\Controller;

use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NominationControllerTest extends WebTestCase
{
    //when not logged in
    //it should not let me vote(?)
    //when logged in
    //when I make a new nomination
    //when I vote Yes on a nomination
    //it should show on the page
    //when I vote No on a nomination
    //it should show on the page

    public function testListCurrentNominationsWithVoteCounts()
    {
        $client = static::createClient();

        $listPage = $client->request('GET', '/nominations/charities');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('#nominations tbody tr:nth-child(1) .name', 'Foo');
        $this->assertSelectorTextContains('#nominations tbody tr:nth-child(2) .name', 'Bar');
        $this->assertSelectorTextContains('#nominations tbody tr:nth-child(3) .name', 'Baz');
        $this->assertSelectorTextNotContains('#nominations tbody tr:nth-child(4) .name', 'Old Foo');

        //see src/DataFixtures/VoteFixtures.php for vote counts
        $listPage->filter('#nominations tbody tr:nth-child(1) td.yes-votes');
        $form = $listPage->selectButton('1');
        $listPage->filter('#nominations tbody tr:nth-child(1) td.no-votes');
        $form = $listPage->selectButton('1');

        $listPage->filter('#nominations tbody tr:nth-child(2) td.yes-votes');
        $form = $listPage->selectButton('4');
        $listPage->filter('#nominations tbody tr:nth-child(2) td.no-votes');
        $form = $listPage->selectButton('0');

        $listPage->filter('#nominations tbody tr:nth-child(3) td.yes-votes');
        $form = $listPage->selectButton('1');
        $listPage->filter('#nominations tbody tr:nth-child(3) td.no-votes');
        $form = $listPage->selectButton('2');
    }

    public function testListPastNominations()
    {
        $now = new \DateTimeImmutable();
        $oneMonth = new \DateInterval('P1M');
        $lastMonth = $now->sub($oneMonth);
        $month = $lastMonth->format("F");
        $year = $lastMonth->format("Y");

        $client = static::createClient();
        $listPage = $client->request('GET', "/nominations/charities/$month/$year");

        $this->assertSelectorTextNotContains('#nominations tbody tr:nth-child(1) .name', 'Foo');
        $this->assertSelectorTextNotContains('#nominations tbody tr:nth-child(2) .name', 'Bar');
        $this->assertSelectorTextNotContains('#nominations tbody tr:nth-child(3) .name', 'Baz');
        $this->assertSelectorTextContains('#nominations tbody tr:nth-child(4) .name', 'Old Foo');
    }

    public function testListPageVoteYes()
    {
        $client = static::createClient();

        $memberRepository = static::$container->get(MemberRepository::class);
        $member = $memberRepository->findOneByNickname('member-1');
        $client->loginUser($member);

        $listPage = $client->request('GET', '/nominations/charities');
        $this->assertResponseIsSuccessful();

        //$mech->submit_form_ok({form_name => 'form-1-yes'});

        $listPage->filter('#nominations tbody tr:nth-child(1) td.yes-votes');
        $form = $listPage->selectButton('1')->form();
        $newPage = $client->submit($form);
        $this->assertResponseRedirects('/nominations/charities');

        $newPage->filter('#nominations tbody tr:nth-child(1) td.yes-votes');
        $form = $listPage->selectButton('2');
        $newPage->filter('#nominations tbody tr:nth-child(1) td.no-votes');
        $form = $listPage->selectButton('1');
    }
    public function testListPageVoteNo()
    {
        $client = static::createClient();

        $memberRepository = static::$container->get(MemberRepository::class);
        $member = $memberRepository->findOneByNickname('member-1');
        $client->loginUser($member);

        $listPage = $client->request('GET', '/nominations/charities');
        $this->assertResponseIsSuccessful();

        //$mech->submit_form_ok({form_name => 'form-1-no'});

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

    public function testListPageWithdrawYesVote()
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
    public function testListPageWithdrawNoVote()
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
    public function testNominateCharity()
    {
        $client = static::createClient();

        $memberRepository = static::$container->get(MemberRepository::class);
        $member = $memberRepository->findOneByNickname('member-1');
        $client->loginUser($member);

        $nominatePage = $client->request('GET', '/nominate/charity');
        $this->assertResponseIsSuccessful();

        $form = $nominatePage->selectButton('nominate')->form();
        $listPage = $client->submit($form, [
            'nomination[name]' => 'Foo Bar',
            'nomination[pitch]' => 'This is a great charity!',
        ]);
        $this->assertResponseRedirects('/nominations/charities');

        //$listPage->filter('#nominations tbody tr:nth-child(4) td.name');
        $this->assertSelectorTextContains('#nominations tbody tr:nth-child(4) td.name', 'Foo Bar');
    }
}
