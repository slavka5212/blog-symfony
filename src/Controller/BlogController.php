<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Form\AuthorFormType;
use App\Form\BlogPostFormType;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @return Response
     */
    public function index(): Response
    {

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController'
        ]);
    }

    public function list(ManagerRegistry $doctrine) : Response
    {
        //$posts = $doctrine->getRepository(BlogPost::class)->findBy([], ['date' => 'DESC']);
        $posts = $doctrine->getRepository(BlogPost::class)->findWithCommentsCount();

        return $this->render('blog/list.html.twig', ['posts' => $posts, 'message' => null]);
    }

    public function show(ManagerRegistry $doctrine, $id, Request $request) : Response
    {

        $post = $doctrine->getRepository(BlogPost::class)->find($id);

        if (!$post) {
            // cause the 404 page not found to be displayed
            throw $this->createNotFoundException();
        }

        $comments = $doctrine->getRepository(BlogPost::class)->getComments($id);

        $post->setCommentsCount(sizeof($comments));

        $comment = new Comment();

        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setBlogPost($id);
            $comment->setDate(new DateTime("now"));

            $entityManager = $doctrine->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirect($request->getUri());
        }

        return $this->render('blog/detail.html.twig',
            ['post' => $post, 'form' => $form->createView(), 'comments' => $comments]);
    }

    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $post = new BlogPost();

        $form = $this->createForm(BlogPostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $post->setDate(new DateTime("now"));

            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();


            return $this->redirectToRoute('blog_list',
                ['message' => 'New blog post created '.$post->getId()]);
        }


        return$this->render('blog/create.html.twig', ['form' => $form->createView()]);
    }

    public function addAuthor(ManagerRegistry $doctrine, Request $request): Response
    {
        $author = new Author();


        $form = $this->createForm(AuthorFormType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($author);
            $entityManager->flush();

            return $this->redirectToRoute('blog_list',
                ['message' => 'Author saved '.$author->getId()]);
        }

        return $this->render('author/new.html.twig', ['form' => $form->createView()]);
    }

}
