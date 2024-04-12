<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Form\UserType;
use App\Form\ThreadType;
use App\Form\CommentType;
use App\Form\NewPasswordType;
use App\Form\DeleteThreadType;
use App\Form\DeleteCommentType;
use App\Form\PasswordConfirmType;
use App\Repository\UserRepository;
use App\Repository\ThreadRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
    public function delete(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, TokenStorageInterface $tokenStorage): Response
    {
        /** @var User $user */
        $user = $this->getUser();


        $deleteForm = $this->createForm(PasswordConfirmType::class, $user);

        $deleteForm->handleRequest($request);

        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $password = $deleteForm->get('password')->getData();
            $passwordConfirm = $deleteForm->get('passwordConfirm')->getData();

            if ($password !== $passwordConfirm) {
                $this->addFlash('danger', "The passwords aren't the same");
                return $this->redirectToRoute('profile.delete');
            }

            if (!$passwordHasher->isPasswordValid($user, $password)) {
                $this->addFlash(
                    'danger',
                    'Incorrect password...'
                );
                return $this->redirectToRoute('profile.delete');
            }

            try {
                $tokenStorage->setToken(null);
                $em->remove($user);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Account deleted!'
                );
                return $this->redirectToRoute('signup');
            } catch (Exception $e) {
                $this->addFlash(
                    'danger',
                    'Error when trying to delete the account...'
                );
                return $this->redirectToRoute('profile.delete');
            }
        }
        return $this->render('profile/delete.html.twig', [
            'deleteForm' => $deleteForm
        ]);
    }

    #[Route(path: '/profile/threads', name: 'profile.threads')]
    public function threads()
    {
        return $this->render('profile/threads/index.html.twig');
    }


    #[Route('/profile/thread/{id}/edit', name: "threads.edit", requirements: ['id' => '\d+'])]
    public function editThread(Request $request, int $id, ThreadRepository $threadRepository, EntityManagerInterface $em,  HtmlSanitizerInterface $htmlSanitizer): Response
    {


        $thread = $threadRepository->find($id);
        if (!$thread) {
            $this->addFlash('danger', 'The ressource requested does not exists');
            return $this->redirectToRoute('profile.threads');
        }

        /** @var User */
        $user = $this->getUser();
        if ($thread->getAuthor()->getId() !== $user->getId()) {
            $this->addFlash('warning', 'You have been redirected here because you are not authorized to access the page you requested.');
            return $this->redirectToRoute('threads');
        }
        $form = $this->createForm(ThreadType::class, $thread);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $thread->setUpdatedAt(new \DateTimeImmutable());
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

            $this->addFlash('success', 'Thread successfully updated');

            return $this->redirectToRoute('profile.threads');
        }

        return $this->render('profile/threads/edit.html.twig', [
            'thread' => $thread,
            'form' => $form
        ]);
    }
    #[Route('/profile/thread/{id}/delete', name: "threads.delete", requirements: ['id' => '\d+'])]
    public function deleteThread(Request $request, int $id, ThreadRepository $threadRepository, EntityManagerInterface $em): Response
    {

        $thread = $threadRepository->find($id);

        if (!$thread) {
            $this->addFlash('danger', 'The ressource requested does not exists');
            return $this->redirectToRoute('profile.threads');
        }

        /** @var User */
        $user = $this->getUser();
        if ($thread->getAuthor()->getId() !== $user->getId()) {
            $this->addFlash('warning', 'You have been redirected here because you are not authorized to access the page you requested.');
            return $this->redirectToRoute('threads');
        }

        $form = $this->createForm(DeleteThreadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $confirmation = $form->get('confirmation')->getData();

            if ($confirmation !== "Yes, delete my thread") {
                $this->addFlash('danger', 'Please enter the correct sentence to confirm the deletion of the thread');
                return $this->redirectToRoute("threads.delete", ['id' => $thread->getId()]);
            }


            foreach ($thread->getCategories() as $category) {
                $thread->removeCategory($category);
            }

            foreach ($thread->getComments() as $comment) {
                $em->remove($comment);
            }

            // il faut bien flush une première pour supprimer les entrées des autres tables associées au thread avant d'essayer de supprimer le thread sinon on a le problème des foreign keys. 
            $em->flush();

            $em->remove($thread);
            $em->flush();

            $this->addFlash('success', 'Thread successfully deleted');

            return $this->redirectToRoute('profile.threads');
        }

        return $this->render('profile/threads/delete.html.twig', [
            'thread' => $thread,
            'form' => $form
        ]);
    }

    #[Route(path: '/profile/comments', name: 'profile.comments')]
    public function comments()
    {
        return $this->render('profile/comments/index.html.twig');
    }

    #[Route('/profile/comment/{id}/edit', name: "comments.edit", requirements: ['id' => '\d+'])]
    public function editComment(Request $request, int $id, CommentRepository $commentRepository, EntityManagerInterface $em,  HtmlSanitizerInterface $htmlSanitizer): Response
    {

        $comment = $commentRepository->find($id);
        if (!$comment) {
            $this->addFlash('danger', 'The ressource requested does not exists');
            return $this->redirectToRoute('profile.comments');
        }

        /** @var User */
        $user = $this->getUser();
        if ($comment->getUser()->getId() !== $user->getId()) {
            $this->addFlash('warning', 'You have been redirected here because you are not authorized to access the page you requested.');
            return $this->redirectToRoute('threads');
        }
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUpdatedAt(new \DateTimeImmutable());
            $sanitizedContent =  $htmlSanitizer->sanitize($form->get('content')->getData());
            $comment->setContent($sanitizedContent);

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Comment successfully updated');

            return $this->redirectToRoute('profile.comments');
        }

        return $this->render('profile/comments/edit.html.twig', [
            'comment' => $comment,
            'form' => $form
        ]);
    }


    #[Route('/profile/comment/{id}/delete', name: "comments.delete", requirements: ['id' => '\d+'])]
    public function deleteComment(Request $request, int $id, CommentRepository $commentRepository, EntityManagerInterface $em): Response
    {

        $comment = $commentRepository->find($id);
        if (!$comment) {
            $this->addFlash('danger', 'The ressource requested does not exists');
            return $this->redirectToRoute('profile.comments');
        }
        /** @var User */
        $user = $this->getUser();
        if ($comment->getUser()->getId() !== $user->getId()) {
            $this->addFlash('warning', 'You have been redirected here because you are not authorized to access the page you requested.');
            return $this->redirectToRoute('threads');
        }
        $form = $this->createForm(DeleteCommentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $confirmation = $form->get('confirmation')->getData();

            if ($confirmation !== "Yes, delete my comment") {
                $this->addFlash('danger', 'Please enter the correct sentence to confirm the deletion of the comment');
                return $this->redirectToRoute("comments.delete", ['id' => $comment->getId()]);
            }

            $em->remove($comment);
            $em->flush();

            $this->addFlash('success', 'Comment successfully deleted');

            return $this->redirectToRoute('profile.comments');
        }

        return $this->render('profile/comments/delete.html.twig', [
            'comment' => $comment,
            'form' => $form
        ]);
    }
}
