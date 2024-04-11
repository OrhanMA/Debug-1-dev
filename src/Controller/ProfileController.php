<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $em->persist($user);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Profile updated!'
                );
                return $this->redirectToRoute('profile');
            } catch (Exception $e) {
                throw $e;
                $this->addFlash(
                    'danger',
                    'Error when trying to update profile...'
                );
            } finally {
                return $this->redirectToRoute('profile');
            }
        }


        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'form' => $form
        ]);
    }
}
