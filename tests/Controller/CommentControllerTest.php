<?php

namespace App\Tests\Controller;

use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentControllerTest extends WebTestCase
{
    //when not logged in
    //it should not let me comment
    //when logged in
    //when I comment on a nomination
    //it should show on the page

    public function testCommentOnNomination()
    {
        $client = static::createClient();

        $memberRepository = static::$container->get(MemberRepository::class);
        $member = $memberRepository->findOneByNickname('member-1');
        $client->loginUser($member);

        $listPage = $client->request('GET', '/nominations/charities');
        $this->assertResponseIsSuccessful();

        //easier than looking up id from DB
        $link = $listPage->selectLink('Bar')->link();
        $showPage = $client->click($link);
        $this->assertResponseIsSuccessful();

        $form = $showPage->selectButton('Save Comment')->form();
        $showPage = $client->submit($form, [
            'comment[comment_text]' => 'This is a great charity!',
        ]);

        $path = str_replace($link->getUri(), 'http://localhost', '');
        $this->assertResponseRedirects($path);
        $client->followRedirect();

        //assert comment is on the page
        //.comment .comment-details
        $this->assertSelectorTextContains('#comments .comment-text', 'This is a great charity!');
        //assertEqual('This is a great charity!',
        //$showPage->filter("#comments .comment-text")->text();
    }

    public function testListCommentsOnNomination()
    {
        $client = static::createClient();

        $listPage = $client->request('GET', '/nominations/charities');
        $this->assertResponseIsSuccessful();

        //easier than looking up id from DB
        $link = $listPage->selectLink('Baz')->link();
        $showPage = $client->click($link);
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('#comments .comment-text', "What a great charity!");
    }
}
