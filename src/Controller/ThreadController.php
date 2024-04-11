<?php

namespace App\Controller;

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
        return $this->render('threads/create/index.html.twig');
    }
}
