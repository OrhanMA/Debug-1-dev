<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vote;
use App\Repository\ThreadRepository;
use App\Repository\UserRepository;
use App\Repository\VoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VoteController extends AbstractController
{
    #[Route('/vote/thread/{id}', name: 'vote.thread.up')]
    public function index(Request $request, VoteRepository $voteRepository, ThreadRepository $threadRepository, int $id, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        /** @var User */
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('warning', 'You have to be signed in to vote a thread');
            return $this->redirectToRoute('signin');
            // user doit être connecté pour voter
        }

        $thread = $threadRepository->find($id);


        if (!$thread) {
            $this->addFlash('danger', 'Thread not found');
            return $this->redirectToRoute('threads');
        }

        if ($thread->getAuthor()->getId() === $user->getId()) {
            $this->addFlash('danger', "You can't vote your own thread");
            return $this->redirectToRoute('threads.show', ['id' => $id]);
            // user ne peut pas voter son propre thread
        }

        $choice = $request->get('vote');

        $vote = new Vote();
        $vote->setCreatedAt(new \DateTimeImmutable());
        $vote->setThread($thread);
        $vote->setUser($user);
        if ($choice === "up") {
            $vote->setLike(true);
            $this->addFlash('success', 'You just liked the thread');
        } else if ($choice === "down") {
            $vote->setLike(true);
            $this->addFlash('success', 'You just disliked the thread');
        }

        $em->persist($vote);
        $em->flush(); // Flush after persisting the vote

        $user->addVote($vote);
        $thread->addVote($vote);

        return $this->redirectToRoute('threads.show', ['id' => $id]);
    }
}
