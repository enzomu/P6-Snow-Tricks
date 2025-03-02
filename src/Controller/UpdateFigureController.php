<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateFigureController extends AbstractController
{
    #[Route('/update_figure', name: 'update_figure')]
    public function index(): Response
    {
        return $this->render('figure_update.html.twig');
    }

}