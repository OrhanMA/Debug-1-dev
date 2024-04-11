<?php

namespace App\Controller;

use App\Repository\ThreadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ThreadController extends AbstractController
{
    #[Route('/threads', name: 'threads')]
    public function index(): Response
    {
        return $this->render('threads/index.html.twig', [
            'controller_name' => 'ThreadController',
        ]);
    }

    #[Route('/threads/create', name: "threads.create")]
    public function create(): Response
    {
        return $this->render('threads/create.html.twig');
    }

    #[Route('/threads/{id}', name: 'threads.show', requirements: ['id' => '\d+'])]
    public function show(int $id, ThreadRepository $threadRepository): Response
    {
        $thread = $threadRepository->find($id);

        return $this->render('threads/show.html.twig', [
            'thread' => $thread
        ]);
    }
}
