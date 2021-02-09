<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\Vote;
use App\Form\VoteType;
use App\Repository\VoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/vote")
 */
class VoteController extends AbstractController
{
    /**
     * @Route("/", name="vote_new", methods={"POST"})
     */
    public function new(Request $request): Response
    {
        //TODO: Replace with logged-in user
        $member = $this->getDoctrine()->getRepository(Member::class)->findAll()[0];

        $vote = new Vote();
        $vote->setMember($member);
        $now = new \DateTimeImmutable();
        $vote->setCreatedAt($now);
        $form = $this->createForm(VoteType::class, $vote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($vote);
            $entityManager->flush();

            return $this->redirectToRoute('nomination_index');
        }

        return $this->render('vote/new.html.twig', [
            'vote' => $vote,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="vote_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Vote $vote): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vote->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($vote);
            $entityManager->flush();
        }

        return $this->redirectToRoute('vote_index');
    }
}
