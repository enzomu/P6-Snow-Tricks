<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    #[Route('/figure/{id}/add-media', name: 'figure_add_media')]
    public function addMedia(int $id, FigureRepository $figureRepository): Response
    {
        $figure = $figureRepository->find($id);
        if (!$figure) {
            throw $this->createNotFoundException('Figure non trouvée');
        }

        $mediaGallery = $figure->getMediaGallery() ?? [];
        $mediaGallery[] = '';
        $figure->setMediaGallery($mediaGallery);

        $figureRepository->save($figure, true);

        return $this->render('figure/_media_gallery.html.twig', [
            'figure' => $figure
        ]);
    }

    #[Route('/figure/{id}/remove-media/{index}', name: 'figure_remove_media')]
    public function removeMedia(int $id, int $index, FigureRepository $figureRepository): Response
    {
        $figure = $figureRepository->find($id);
        if (!$figure) {
            throw $this->createNotFoundException('Figure non trouvée');
        }

        $mediaGallery = $figure->getMediaGallery() ?? [];
        if (isset($mediaGallery[$index])) {
            array_splice($mediaGallery, $index, 1);
            $figure->setMediaGallery($mediaGallery);
            $figureRepository->save($figure, true);
        }

        return $this->render('figure/_media_gallery.html.twig', [
            'figure' => $figure
        ]);
    }

    #[Route('/figure/{id}/update-media/{index}', name: 'figure_update_media', methods: ['POST'])]
    public function updateMedia(int $id, int $index, Request $request, FigureRepository $figureRepository): Response
    {
        $figure = $figureRepository->find($id);
        if (!$figure) {
            throw $this->createNotFoundException('Figure non trouvée');
        }

        $mediaGallery = $figure->getMediaGallery() ?? [];
        if (isset($mediaGallery[$index])) {
            $mediaGallery[$index] = $request->request->get('media_url');
            $figure->setMediaGallery($mediaGallery);
            $figureRepository->save($figure, true);
        }

        return $this->render('figure/_media_gallery.html.twig', [
            'figure' => $figure
        ]);
    }
}