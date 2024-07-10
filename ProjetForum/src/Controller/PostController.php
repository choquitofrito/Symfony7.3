<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    #[Route('/posts', name: 'post_index', methods: ['GET'])]
    public function index(Request $request, PostRepository $postRepository): Response
    {

        // renvoie que les 10 premiers posts
        // si on change de page, on charge un nouveau lot de 10
        $page = $request->query->getInt('page', 1);
        $limit = 10;
        $posts = $postRepository->findBy([], null, $limit, ($page - 1) * $limit);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'page_actuelle' => $page,
            'total_pages' => ceil(count($postRepository->findAll()) / $limit),
        ]);
    }


    #[Route('/posts/new', name: 'post_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setDateCreation(new \DateTime());
            $post->setUser($this->getUser());
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/posts/{id}', name: 'post_show', methods: ['GET', 'POST'])]
    public function show(
        Post $post,
        Request $request,
        CommentaireRepository $commentaireRepository,
        EntityManagerInterface $em,
        SerializerInterface $serializer
    ): Response {
        $commentaire = new Commentaire();
        $commentaireForm = $this->createForm(CommentaireType::class, $commentaire);
        $commentaireForm->handleRequest($request);

        if ($commentaireForm->isSubmitted() && $commentaireForm->isValid()) {
            $commentaire->setPost($post);
            $commentaire->setUser($this->getUser()); // Assurez-vous que l'utilisateur est connecté
            $commentaire->setDateCreation(new \DateTime());
            $em->persist($commentaire);
            $em->flush();

            // dd($request->isXmlHttpRequest());
            if ($request->isXmlHttpRequest()) {
                
                // $jsonContent = $serializer->serialize($commentaire, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['post','user']]);

                $jsonContent = $serializer->serialize($commentaire, 'json', ['groups' => 'commentaire:read']);
                return new JsonResponse($jsonContent, Response::HTTP_CREATED, [], true);
            }

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        $commentaires = $commentaireRepository->findBy(['post' => $post]);

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'commentaires' => $commentaires,
            'commentaire_form' => $commentaireForm->createView(),
        ]);
    }

    #[Route('/posts/{id}/edit', name: 'post_edit')]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setDateUpdate(new \DateTime());
            $entityManager->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    #[Route('/posts/{id}/delete', name: 'post_delete')]
    public function delete(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {

        // dd($post);
        // if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        // }
        return new JsonResponse(['message' => 'delete ok']);
        // return $this->redirectToRoute('post_index');
    }

    #[Route('/api/posts', name: 'api_post_index', methods: ['GET'])]
    public function apiIndex(
        Request $request,
        PostRepository $postRepository,
        SerializerInterface $serializer
    ): Response {
        $page = $request->query->getInt('page', 1); // si pas de page, valeur par défaut 1
        $limit = 10; // obtenir max 10 posts à la fois
        $posts = $postRepository->findBy([], null, $limit, ($page - 1) * $limit);


        $jsonContent = $serializer->serialize($posts, 'json', ['groups' => 'post:read']);

        return new Response($jsonContent, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
