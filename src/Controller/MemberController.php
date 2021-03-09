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
}
