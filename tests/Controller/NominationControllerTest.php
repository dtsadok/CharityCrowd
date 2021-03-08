<?php

namespace App\Tests\Controller;

use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NominationControllerTest extends WebTestCase
{
    //when not logged in
    //it should not let me make a new nomination
    //when logged in
    //when I make a new nomination
    //it should show on the page

    public function testListCurrentNominationsWithVoteCounts()
    {
        $client = static::createClient();

        $listPage = $client->request('GET', '/nominations/charities');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('#nominations tbody tr:nth-child(1) .name', 'Foo');
        $this->assertSelectorTextContains('#nominations tbody tr:nth-child(2) .name', 'Bar');
        $this->assertSelectorTextContains('#nominations tbody tr:nth-child(3) .name', 'Baz');
        $this->assertSelectorNotExists('#nominations tbody tr:nth-child(4) .name'); //Old Nomination

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

        $this->assertSelectorExists('#nominations tbody tr:nth-child(1) .name');
        $this->assertSelectorTextContains('#nominations tbody tr:nth-child(1) .name', 'Old Nomination');
        $this->assertSelectorTextNotContains('#nominations tbody tr:nth-child(1) .name', 'Foo');
        $this->assertSelectorNotExists('#nominations tbody tr:nth-child(2) .name'); //should only see one nomination
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
        $client->followRedirect();

        //$listPage->filter('#nominations tbody tr:nth-child(4) td.name');
        $this->assertSelectorTextContains('#nominations tbody tr:nth-child(4) td.name', 'Foo Bar');
    }
}
