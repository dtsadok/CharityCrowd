<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\Nomination;
use App\Entity\Vote;
use App\Form\NominationType;
use App\Form\VoteType;
use App\Repository\NominationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NominationController extends AbstractController
{
    /**
     * @Route("/nominations/charities", name="nomination_index", methods={"GET"})
     */
    public function index(NominationRepository $nominationRepository): Response
    {
        //TODO: Replace with logged-in user
        $member = $this->getDoctrine()->getRepository(Member::class)->findAll()[0];

        $nominations = $nominationRepository->findAllWithVotes();

dump($nominations);

        //TODO: just cache vote_count's in nominations table
        $nominationsMerged = Array();
        foreach ($nominations as $nomination)
        {
            $id = $nomination[0]->getId();
            if (!array_key_exists($id, $nominationsMerged))
            {
                $nominationsMerged[$id] = $nomination;
                $nominationsMerged[$id]['yes_votes'] = 0;
                $nominationsMerged[$id]['no_votes'] = 0;
            }

            if ($nomination['value'] == 'Y')
            {
                $nominationsMerged[$id]['yes_votes'] = $nomination['vote_count'];
            }
            else if ($nomination['value'] == 'N')
            {
                $nominationsMerged[$id]['no_votes'] = $nomination['vote_count'];
            }

            //forms for voting
            $yesVote = new Vote();
            $yesVote->setMember($member)->setNomination($nomination[0])->setValue('Y');
            $voteYesButton = $this->createForm(VoteType::class, $yesVote,
                ['action' => $this->generateUrl('vote_new')]
            );
            $nominationsMerged[$id]['vote']['yes'] = $voteYesButton->createView();
            $noVote = new Vote();
            $noVote->setMember($member)->setNomination($nomination[0])->setValue('N');
            $voteNoButton = $this->createForm(VoteType::class, $noVote,
                ['action' => $this->generateUrl('vote_new')]
            );

            $nominationsMerged[$id]['vote']['no'] = $voteNoButton->createView();
        }

        return $this->render('nomination/index.html.twig', [
            'nominations' => $nominationsMerged
        ]);
    }

    /**
     * @Route("/nominate/charity", name="nomination_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        //TODO: Replace with logged-in user
        $member = $this->getDoctrine()->getRepository(Member::class)->findAll()[0];

        $nomination = new Nomination();
        $nomination->setMember($member);
        $now = new \DateTimeImmutable();
        $nomination->setCreatedAt($now);
        $nomination->setUpdatedAt($now);

        $form = $this->createForm(NominationType::class, $nomination);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($nomination);
            $entityManager->flush();

            return $this->redirectToRoute('nomination_index');
        }

        return $this->render('nomination/new.html.twig', [
            'nomination' => $nomination,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/nomination/{id}", name="nomination_show", methods={"GET"})
     */
    public function show(Nomination $nomination): Response
    {
        return $this->render('nomination/show.html.twig', [
            'nomination' => $nomination,
        ]);
    }

    /**
     * @Route("/nomination/{id}/edit", name="nomination_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Nomination $nomination): Response
    {
        $form = $this->createForm(NominationType::class, $nomination);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('nomination_index');
        }

        return $this->render('nomination/edit.html.twig', [
            'nomination' => $nomination,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/nomination/{id}", name="nomination_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Nomination $nomination): Response
    {
        if ($this->isCsrfTokenValid('delete'.$nomination->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($nomination);
            $entityManager->flush();
        }

        return $this->redirectToRoute('nomination_index');
    }
}
