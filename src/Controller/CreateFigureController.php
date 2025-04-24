<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\FigureType;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateFigureController extends AbstractController
{
    #[Route('/create_figure', name: 'create_figure')]
    public function create(Request $request, FigureRepository $figureRepository): Response
    {
        $this->denyAccessUnlessGranted('FIGURE_CREATE');

        $figure = new Figure();
        $figure->setMediaGallery(['']);

        $user = $this->getUser();
        $figure->setAuthor($user);

        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mediaGallery = array_filter($figure->getMediaGallery(), function($media) {
                return !empty(trim($media));
            });
            $figure->setMediaGallery($mediaGallery);
            $figure->setCreatedAt(new \DateTimeImmutable());
            $figureRepository->save($figure);

            return $this->redirectToRoute('home');
        }

        return $this->render('figure_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
