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

        $nominations = $nominationRepository->findAll();

dump($nominations);

        //generate forms for voting
        $voteForms = [];

        foreach ($nominations as $nomination)
        {
            $id = $nomination->getId();
            $yesVote = new Vote();
            $yesVote->setMember($member)->setNomination($nomination)->setValue('Y');
            $voteYesButton = $this->createForm(VoteType::class, $yesVote,
                ['action' => $this->generateUrl('vote_new')]
            );
            $voteForms[$id]['yes'] = $voteYesButton->createView();
            $noVote = new Vote();
            $noVote->setMember($member)->setNomination($nomination)->setValue('N');
            $voteNoButton = $this->createForm(VoteType::class, $noVote,
                ['action' => $this->generateUrl('vote_new')]
            );

            $voteForms[$id]['no'] = $voteNoButton->createView();
        }

        return $this->render('nomination/index.html.twig', [
            'nominations' => $nominations,
            'voteForms' => $voteForms
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
        $nomination->setYesCount(0);
        $nomination->setNoCount(0);
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
