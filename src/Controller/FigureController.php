<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class FigureController extends AbstractController
{
    #[Route('/figure/{id}/{slug}', name: 'figure')]
    public function show(
        int $id,
        Request $request,
        FigureRepository $figureRepository,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator,
        SluggerInterface $slugger,
        ?string $slug = null,
    ): Response {
        $figure = $figureRepository->findFigureWithComments($id);

        if (!$figure) {
            throw $this->createNotFoundException('Figure non trouvée.');
        }

        $expectedSlug = strtolower($slugger->slug($figure->getName())->toString());

        if ($slug !== $expectedSlug) {
            return $this->redirectToRoute('figure', [
                'id' => $id,
                'slug' => $expectedSlug
            ], 301);
        }

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid() && $this->getUser()) {
            $comment->setAuthor($this->getUser());
            $comment->setFigure($figure);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire ajouté avec succès !');

            return $this->redirectToRoute('figure', ['id' => $id, 'slug' => $expectedSlug]);
        }

        $commentsQuery = $figureRepository->getCommentsQueryForFigure($figure);
        $pagination = $paginator->paginate(
            $commentsQuery,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('figure.html.twig', [
            'figure' => $figure,
            'commentForm' => $commentForm->createView(),
            'comments' => $pagination,
        ]);
    }
}