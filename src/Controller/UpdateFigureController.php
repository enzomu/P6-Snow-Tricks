<?php

namespace App\Controller;

use App\Form\FigureType;
use App\Repository\FigureRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateFigureController extends AbstractController
{
    #[Route('/update_figure/{id}', name: 'update_figure')]
    public function index(int $id, FigureRepository $figureRepository, Request $request): Response
    {
        $figure = $figureRepository->findFigureById($id);

        $this->denyAccessUnlessGranted('FIGURE_EDIT', $figure);

        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mediaGallery = array_filter($figure->getMediaGallery(), function($media) {
                return !empty(trim($media));
            });
            $figure->setMediaGallery($mediaGallery);
            $figure->setUpdatedAt(new \DateTimeImmutable());

            $figureRepository->save($figure);

            return $this->redirectToRoute('home');
        }

        return $this->render('figure_update.html.twig', [
            'figure' => $figure,
            'form' => $form->createView(),
        ]);
    }
}
