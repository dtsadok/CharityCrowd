<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Member;
use App\Entity\Nomination;
use App\Entity\Vote;
use App\Form\CommentType;
use App\Form\NominationType;
use App\Form\VoteType;
use App\Repository\NominationRepository;
use App\Repository\VoteRepository;
use App\Service\MonthYearService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NominationController extends AbstractController
{
    /**
     * @Route("/nominations/charities/{month}/{year}", name="nomination_index", methods={"GET"})
     */
    public function index(string $month=null, int $year=null, NominationRepository $nominationRepository, VoteRepository $voteRepository, MonthYearService $monthYearService): Response
    {
        /** @var \App\Entity\Member $user */
        $member = $this->getUser();

        $now = new \DateTimeImmutable();
        if ($month == null) { $month = $now->format('F'); }
        if ($year == null) { $year = $now->format('Y'); }

        //TODO [SECURITY]: validate input
        $monthNumber = $monthYearService->getMonthNumberFromMonthName($month);

        $nominations = $nominationRepository->findAllForMonth($monthNumber, $year);

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

        $memberVotes = [];
        $votes = $voteRepository->findAllForMemberForMonth($member, $monthNumber, $year);
        foreach ($votes as $vote)
        {
            $memberVotes[ $vote->getNomination()->getId() ] = $vote;
        }

        return $this->render('nomination/index.html.twig', [
            'nominations' => $nominations,
            'voteForms' => $voteForms,
            'memberVotes' => $memberVotes,
            'month' => $month,
            'year' => $year
        ]);
    }

    /**
     * @Route("/nominate/charity", name="nomination_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\Member $user */
        $member = $this->getUser();

        $nomination = new Nomination();
        $nomination->setMember($member);
        $nomination->setYesCount(0);
        $nomination->setNoCount(0);
        $nomination->setPercentage(0);

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
        /** @var \App\Entity\Member $user */
        $member = $this->getUser();

        $comment = new Comment();
        $comment->setNomination($nomination);
        $commentForm = $this->createForm(CommentType::class, $comment,
                ['action' => $this->generateUrl('comment_new')]);

        return $this->render('nomination/show.html.twig', [
            'nomination' => $nomination,
            'form' => $commentForm->createView(),
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
