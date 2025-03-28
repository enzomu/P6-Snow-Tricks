<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FigureController extends AbstractController
{
    #[Route('/figure/{id}', name: 'figure')]
    public function index(int $id, FigureRepository $figureRepository): Response
    {
        $figure = $figureRepository->findFigureById($id);

        return $this->render('figure.html.twig', [
            'figure' => $figure,
        ]);
    }
}