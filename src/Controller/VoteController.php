<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vote;
use App\Repository\CommentRepository;
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
    #[Route('/vote/thread/{id}', name: 'vote.thread')]
    public function thread(Request $request, VoteRepository $voteRepository, ThreadRepository $threadRepository, int $id, EntityManagerInterface $em): Response
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
        $vote = $voteRepository->findOneBy([
            'thread' => $thread->getId(),
            'user' => $user->getId()
        ]);



        if ($vote === null) {
            // si pas encore de vote
            $vote = new Vote();
            $vote->setCreatedAt(new \DateTimeImmutable());
            $vote->setThread($thread);
            $vote->setUser($user);
            if ($choice === "up") {
                $vote->setLike(true);
                $this->addFlash('success', 'You just liked the thread');
            } else {
                $vote->setLike(false);
                $this->addFlash('success', 'You just disliked the thread');
            }
            $em->persist($vote);
        } else {
            $vote->setUpdatedAt(new \DateTimeImmutable());
        }
        if ($choice === "up") {
            if ($vote->isLike()) {
                // si like choisi et thread était déjà liké
                $thread->removeVote($vote);
                $user->removeVote($vote);
                $em->remove($vote);
            } else {
                // si like choisi et thread était disliké
                $vote->setLike(true);
                $this->addFlash('success', 'You just liked the thread');
            }
        } else if ($choice === "down") {
            if ($vote->isLike()) {
                // si dislike choisi et thread était liké
                $vote->setLike(false);
                $this->addFlash('success', 'You just disliked the thread');
            } else {
                // si dislike choisi et thread était disliké
                $thread->removeVote($vote);
                $user->removeVote($vote);
                $em->remove($vote);
            }
        }

        $user->addVote($vote);
        $thread->addVote($vote);



        $em->flush();

        return $this->redirectToRoute('threads.show', ['id' => $id]);
    }

    #[Route('/vote/comment/{id}', name: 'vote.comment')]
    public function comment(Request $request, VoteRepository $voteRepository, CommentRepository $commentRepository, int $id, EntityManagerInterface $em): Response
    {
        /** @var User */
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('warning', 'You have to be signed in to vote a comment');
            return $this->redirectToRoute('signin');
        }

        $comment = $commentRepository->find($id);

        if (!$comment) {
            $this->addFlash('danger', 'Comment not found');
            return $this->redirectToRoute('threads');
        }

        if ($comment->getUser()->getId() === $user->getId()) {
            $this->addFlash('danger', "You can't vote your own comment");
            return $this->redirectToRoute('threads.show', ['id' => $comment->getThread()->getId()]);
        }

        $choice = $request->get('vote');
        $vote = $voteRepository->findOneBy([
            'comment' => $id,
            'user' => $user->getId()
        ]);



        if ($vote === null) {
            // si pas encore de vote
            $vote = new Vote();
            $vote->setCreatedAt(new \DateTimeImmutable());
            $vote->setComment($comment);
            $vote->setUser($user);
            if ($choice === "up") {
                $vote->setLike(true);
                $this->addFlash('success', 'You just liked the comment');
            } else {
                $vote->setLike(false);
                $this->addFlash('success', 'You just disliked the comment');
            }
            $em->persist($vote);
        } else {
            $vote->setUpdatedAt(new \DateTimeImmutable());
        }
        if ($choice === "up") {
            if ($vote->isLike()) {
                // si like choisi et comment était déjà liké
                $comment->removeVote($vote);
                $user->removeVote($vote);
                $em->remove($vote);
            } else {
                // si like choisi et comment était disliké
                $vote->setLike(true);
                $this->addFlash('success', 'You just liked the comment');
            }
        } else if ($choice === "down") {
            if ($vote->isLike()) {
                // si dislike choisi et comment était liké
                $vote->setLike(false);
                $this->addFlash('success', 'You just disliked the comment');
            } else {
                // si dislike choisi et comment était disliké
                $comment->removeVote($vote);
                $user->removeVote($vote);
                $em->remove($vote);
            }
        }

        $user->addVote($vote);
        $comment->addVote($vote);



        $em->flush();

        return $this->redirectToRoute('threads.show', ['id' => $comment->getThread()->getId()]);
    }
}
