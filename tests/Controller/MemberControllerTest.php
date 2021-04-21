<?php

namespace App\Tests\Controller;

use App\Repository\MemberRepository;
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
    public function testChangePassword()
    {
        $client = static::createClient();

        $memberRepository = static::$container->get(MemberRepository::class);
        $member = $memberRepository->findOneByNickname('member-1');
        $client->loginUser($member);

        $changePasswordPage = $client->request('GET', '/password/change');
        $this->assertResponseIsSuccessful();

        $changePasswordPage->filter('#change_password');
        $form = $changePasswordPage->selectButton('Change Password')->form();

        $homePage = $client->submit($form, [
            'old_password' => '1234',
            'password' => '4567',
            'password_confirmation' => '4567',
        ]);

        $this->assertResponseRedirects('/nominations/charities');

        //TODO: test login with new password
    }
}
