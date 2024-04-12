<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Thread;
use App\Form\CommentType;
use App\Form\ThreadType;
use App\Repository\ThreadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

class ThreadController extends AbstractController
{
    #[Route('/threads', name: 'threads')]
    public function index(ThreadRepository
    $threadRepository): Response
    {

        $threads = $threadRepository->findAll();

        return $this->render('threads/index.html.twig', [
            'threads' => $threads
        ]);
    }


    #[Route('/threads/{id}', name: 'threads.show', requirements: ['id' => '\d+'])]
    public function show(int $id, ThreadRepository $threadRepository, Request $request, EntityManagerInterface $em, HtmlSanitizerInterface $htmlSanitizer): Response
    {
        $thread = $threadRepository->find($id);

        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);

        $comment = new Comment();

        if ($form->isSubmitted() && $form->isValid()) {
            $content = $form->get('content')->getData();
            $sanitizedContent = $htmlSanitizer->sanitize($content);

            try {
                $comment->setContent($sanitizedContent);
                $comment->setCreatedAt(new \DateTimeImmutable());
                $comment->setSolution(false);
                $comment->setThread($thread);
                $comment->setUser($this->getUser());

                $em->persist($comment);
                $em->flush();

                $this->addFlash('success', 'Comment posted successfully!');
            } catch (\Throwable $th) {
                $this->addFlash('danger', 'Error posting the comment. Please try again');
            } finally {
                return $this->redirectToRoute('threads.show', ['id' => $id]);
            }
        }

        return $this->render('threads/show.html.twig', [
            'thread' => $thread,
            'form' => $form
        ]);
    }

    #[Route('/threads/create', name: "threads.create")]
    public function create(Request $request, EntityManagerInterface $em, HtmlSanitizerInterface $htmlSanitizer): Response
    {
        $thread = new Thread();
        $user = $this->getUser();

        $form = $this->createForm(ThreadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $thread->setCreatedAt(new \DateTimeImmutable());
            $thread->setAuthor($user);
            $thread->setStatus('open');
            $sanitizedContent =  $htmlSanitizer->sanitize($form->get('content')->getData());
            $thread->setContent($sanitizedContent);
            $thread->setTitle($form->get('title')->getData());
            $thread->setDescription($form->get('description')->getData());

            $categories = $form->get('categories')->getData();

            foreach ($categories as $category) {
                $thread->addCategory($category);
            }

            $em->persist($thread);
            $em->flush();

            return $this->redirectToRoute('profile.threads');
        }

        return $this->render('threads/create.html.twig', [
            'form' => $form
        ]);
    }
}
