<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(FigureRepository $figureRepository): Response
    {
        $figures = $figureRepository->findAllFigures();

        return $this->render('home.html.twig', [
            'figures' => $figures,
        ]);
    }
}