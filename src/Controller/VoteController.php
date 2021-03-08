<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\Nomination;
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\Member $user */
        $member = $this->getUser();

        $voteInfo = $request->request->get('vote');
        $nomination = $this->getDoctrine()->getRepository(Nomination::class)->findOneBy(["id" => $voteInfo["nomination"]]);
        $existingVote = $voteRepository->findOneBy(["member" => $member, "nomination" => $nomination]);

        if (!$nomination->isCurrent())
        {
            return new Response("<h1>Cannot vote on nomination from previous month</h1>");
        }

        if ($existingVote && !$existingVote->isCurrent())
        {
            return new Response("<h1>Cannot change old vote</h1>");
        }

        $entityManager = $this->getDoctrine()->getManager();
        //for updating counts
        $voteRepository = $this->getDoctrine()->getRepository(Vote::class);
        $vote = null;

        if ($existingVote == null)
        {
            $vote = new Vote();
            $vote->setMember($member);
        }
        else if ($existingVote->getValue() != $voteInfo["value"]) //change vote
        {
            $vote = $existingVote;
        }
        else //withdraw vote
        {
            $entityManager->getConnection()->beginTransaction();
            $entityManager->remove($existingVote);
            $entityManager->flush();
            $existingVote->getNomination()->setVoteCounts($voteRepository);
            $entityManager->persist($existingVote->getNomination());
            $entityManager->flush();
            $entityManager->getConnection()->commit();

            return $this->redirectToRoute('nomination_index');
        }

        //if we are here, create or update vote

        //TODO: put this in model validation
        if ($voteInfo["value"] != 'Y' && $voteInfo["value"] != 'N')
        {
            //TODO: 422(?) error code
            return new Response("<h1>Vote must be Y or N, received ${voteInfo["value"]}</h1>");
        }

        $form = $this->createForm(VoteType::class, $vote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->getConnection()->beginTransaction();

                $entityManager->persist($vote);
                $entityManager->flush();

                $vote->getNomination()->setVoteCounts($voteRepository);
                $entityManager->persist($vote->getNomination());
                $entityManager->flush();

                $entityManager->getConnection()->commit();

                return $this->redirectToRoute('nomination_index');

            } catch (UniqueConstraintViolationException $e) { //should never happen
                //TODO: 401(?) error code
                return new Response("<h1>Already voted</h1>");
            }
        }
        else
        {
            return new Response("<h1>Error (CSRF?)</h1>");
        }

        /*
        return $this->render('vote/new.html.twig', [
            'vote' => $vote,
            'form' => $form->createView(),
        ]);
        */
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
