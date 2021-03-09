<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MemberControllerTest extends WebTestCase
{
    public function testSignUp()
    {
        $client = static::createClient();

        $signupPage = $client->request('GET', '/signup');
        $this->assertResponseIsSuccessful();

        $signupPage->filter('#signup');
        $form = $signupPage->selectButton('Sign Up')->form();

        $homePage = $client->submit($form, [
            'member[nickname]' => 'new_member',
            'member[password][first]' => '1234',
            'member[password][second]' => '1234',
        ]);

        $this->assertResponseRedirects('/login');

        //TODO: Follow redirect and test login
    }
    public function testSignUpWithMismatchedPasswords()
    {
        $client = static::createClient();

        $signupPage = $client->request('GET', '/signup');
        $this->assertResponseIsSuccessful();

        $signupPage->filter('#signup');
        $form = $signupPage->selectButton('Sign Up')->form();

        $homePage = $client->submit($form, [
            'member[nickname]' => 'new_member',
            'member[password][first]' => '1234',
            'member[password][second]' => '5678',
        ]);

        $this->assertResponseStatusCodeSame(422);
    }
}
