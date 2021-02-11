<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\Vote;
use App\Form\VoteType;
use App\Repository\VoteRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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
    public function new(Request $request, VoteRepository $voteRepository): Response
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
            try {
                $entityManager->getConnection()->beginTransaction();

                $entityManager->persist($vote);
                $entityManager->flush();

                $yesCount = $voteRepository->countYesVotesByNomination($vote->getNomination());
                $vote->getNomination()->setYesCount(intval($yesCount[0]["count"]));
                $noCount = $voteRepository->countNoVotesByNomination($vote->getNomination());
                $vote->getNomination()->setNoCount(intval($noCount[0]["count"]));

                $entityManager->persist($vote->getNomination());
                $entityManager->flush();

                $entityManager->getConnection()->commit();

                return $this->redirectToRoute('nomination_index');
            } catch (UniqueConstraintViolationException $e) {
                //TODO: 401(?) error code
                return new Response("<h1>Already voted!</h1>");
            }
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

        //return $this->redirectToRoute('vote_index');
        return $this->response("<h1>deleted</h1>");
    }
}
