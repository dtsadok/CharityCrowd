<?php

namespace App\Controller;

use App\Entity\Member;
use App\Form\MemberType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MemberController extends AbstractController
{
    /**
     * @Route("/signup", name="signup", methods={"GET","POST"})
     */
    public function signup(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $member = new Member();
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $member->setPassword(
                $passwordEncoder->encodePassword($member, $member->getPassword())
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($member);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        $response = $this->render('member/signup.html.twig', [
            'form' => $form->createView()
        ]);
        //I'm sure there's a better "Symfony" way to do this...
        if ($form->isSubmitted() && !$form->isValid()) {
            $response->setStatusCode(422);
        }
        return $response;
    }

    /**
     * @Route("/password/change", name="password_change", methods={"GET","POST"})
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        //with some help from https://penguin-arts.com/how-to-make-change-password-in-symfony/

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var \App\Entity\Member $member */
        $member = $this->getUser();

        $oldPassword = $request->get('old_password');
        $newPassword = $request->get('password');
        $newPassword2 = $request->get('password_confirmation');

        if ($oldPassword && $newPassword == $newPassword2 &&
            $passwordEncoder->isPasswordValid($member, $oldPassword))
        {
            $member->setPassword(
                $passwordEncoder->encodePassword($member, $newPassword)
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($member);
            $entityManager->flush();

            return $this->redirectToRoute('nomination_index');
        }
        else if ($oldPassword)
        {
            $response = new Response("<h1>Either the password was incorrect, or the new passwords don't match.</h1>");
            $response->setStatusCode(422);
            return $response;
        }

        return $this->render('member/change_password.html.twig', []);
    }
}
