<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CommentController extends AbstractController
{
    #[Route('/figure/{id}/comment', name: 'add_comment', methods: ['POST'])]
    public function addComment(
        Figure $figure,
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        if (!$this->getUser() || !$this->getUser()->isVerified()) {
            throw $this->createAccessDeniedException('Vous devez être connecté et vérifié pour commenter.');
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setFigure($figure);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire ajouté avec succès.');
        }

        $slug = strtolower($slugger->slug($figure->getName())->toString());

        return $this->redirectToRoute('figure', [
            'id' => $figure->getId(),
            'slug' => $slug
        ]);
    }
}