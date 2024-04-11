<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Form\UserType;
use App\Form\NewPasswordType;
use App\Form\PasswordConfirmType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        // j'ai ajouté le commentaire en haut pour supprimer l'erreur sur la méthode getPassword qui posait problème avec inteliphense
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $passwordForm = $this->createForm(NewPasswordType::class, $user);
        $passwordForm->handleRequest($request);


        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {

            $password = $passwordForm->get('password')->getData();
            $newPassword = $passwordForm->get('newPassword')->getData();


            if (!$passwordHasher->isPasswordValid($user, $password)) {
                $this->addFlash(
                    'danger',
                    'Incorrect password...'
                );
                return $this->redirectToRoute('profile');
            }


            try {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
                $em->persist($user);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Password updated!'
                );
                return $this->redirectToRoute('profile');
            } catch (Exception $e) {
                throw $e;
                $this->addFlash(
                    'danger',
                    'Error when trying to update password...'
                );
            } finally {
                return $this->redirectToRoute('profile');
            }
        }

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
            'form' => $form,
            'passwordForm' => $passwordForm
        ]);
    }

    #[Route('/user/{id}', name: 'profile.public', requirements: ['id' => '\d+'])]
    public function show(Request $request, UserRepository $userRepository, int $id): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            $previousRoute = $request->headers->get('referer');
            if ($previousRoute) {
                return $this->redirect($previousRoute);
            } else {
                return $this->redirectToRoute('threads');
            }
        }

        /** @var User $loggedInUser */
        $loggedInUser = $this->getUser();

        if ($loggedInUser && $loggedInUser->getId() == $user->getId()) {
            return $this->redirectToRoute('profile');
        }


        return $this->render('profile/show.html.twig', [
            'user' => $user
        ]);
    }

    #[Route(path: '/profile/delete', name: 'profile.delete')]
    public function delete(Request $request): Response
    {
        $user = $this->getUser();


        $deleteForm = $this->createForm(PasswordConfirmType::class, $user);

        $deleteForm->handleRequest($request);

        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            // logique de suppression
        }
        return $this->render('profile/delete.html.twig', [
            'deleteForm' => $deleteForm
        ]);
    }
}
