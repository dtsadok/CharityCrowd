<?php

namespace App\Tests\Controller;

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

    public function testListNominationsWithVoteCounts()
    {
        $client = static::createClient();
        $listPage = $client->request('GET', '/nominations/charities');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('#nominations tbody tr:nth-child(1) .name', 'Foo');
        $this->assertSelectorTextContains('#nominations tbody tr:nth-child(2) .name', 'Bar');
        $this->assertSelectorTextContains('#nominations tbody tr:nth-child(3) .name', 'Baz');

        //see src/DataFixtures/VoteFixtures.php for vote counts
        $listPage->filter('#nominations tbody tr:nth-child(1) td.yes-votes');
        $form = $listPage->selectButton('1');
        $listPage->filter('#nominations tbody tr:nth-child(1) td.no-votes');
        $form = $listPage->selectButton('1');

        $listPage->filter('#nominations tbody tr:nth-child(2) td.yes-votes');
        $form = $listPage->selectButton('3');
        $listPage->filter('#nominations tbody tr:nth-child(2) td.no-votes');
        $form = $listPage->selectButton('1');

        $listPage->filter('#nominations tbody tr:nth-child(3) td.yes-votes');
        $form = $listPage->selectButton('1');
        $listPage->filter('#nominations tbody tr:nth-child(3) td.no-votes');
        $form = $listPage->selectButton('2');
    }

    public function testListPageVoteYes()
    {
        $client = static::createClient();
        $listPage = $client->request('GET', '/nominations/charities');
        $this->assertResponseIsSuccessful();

        //$mech->submit_form_ok({form_name => 'form-1-yes'});

        $listPage->filter('#nominations tbody tr:nth-child(1) td.yes-votes');
        $form = $listPage->selectButton('1')->form();
        $client->submit($form);
        $this->assertResponseRedirects('/nominations/charities');
    }
    public function testListPageVoteNo()
    {
        $client = static::createClient();
        $listPage = $client->request('GET', '/nominations/charities');
        $this->assertResponseIsSuccessful();

        //$mech->submit_form_ok({form_name => 'form-1-no'});

        $listPage->filter('#nominations tbody tr:nth-child(1) td.no-votes');
        $form = $listPage->selectButton('1')->form();
        $client->submit($form);
        $this->assertResponseRedirects('/nominations/charities');
    }
    public function testNominateCharity()
    {
        $client = static::createClient();
        $nominatePage = $client->request('GET', '/nominate/charity');
        $this->assertResponseIsSuccessful();

        $form = $nominatePage->selectButton('nominate')->form();
        $listPage = $client->submit($form, [
            'nomination[name]' => 'Foo Bar',
            'nomination[pitch]' => 'This is a great charity!',
        ]);
        $this->assertResponseRedirects('/nominations/charities');

        $this->assertSelectorTextContains('#nominations .name', 'Foo Bar');
    }
}
