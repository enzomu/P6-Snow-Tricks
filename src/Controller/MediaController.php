<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\FigureType;
use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class MediaController extends AbstractController
{
    #[Route('/figure/{id}/add-media', name: 'figure_add_media')]
    public function addMedia(int $id, FigureRepository $figureRepository): Response
    {
        $figure = $figureRepository->findFigureById($id);

        $mediaGallery = $figure->getMediaGallery() ?: [];
        $mediaGallery[] = '';
        $figure->setMediaGallery($mediaGallery);

        $figureRepository->save($figure);

        return $this->redirectToRoute('update_figure', ['id' => $figure->getId()]);
    }

    #[Route('/figure/{id}/remove-media/{index}', name: 'figure_remove_media')]
    public function removeMedia(int $id, int $index, FigureRepository $figureRepository): Response
    {
        $figure = $figureRepository->findFigureById($id);

        $mediaGallery = $figure->getMediaGallery();

        if (isset($mediaGallery[$index])) {
            unset($mediaGallery[$index]);
            $mediaGallery = array_values($mediaGallery);
            $figure->setMediaGallery($mediaGallery);

            $figureRepository->save($figure);
        }

        return $this->redirectToRoute('update_figure', ['id' => $figure->getId()]);
    }
}