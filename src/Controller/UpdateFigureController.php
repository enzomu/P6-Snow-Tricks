<?php

namespace App\Controller;

use App\Form\FigureType;
use App\Repository\FigureRepository;
use App\Services\FileUploader;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UpdateFigureController extends AbstractController
{
    #[Route('/update_figure/{id}', name: 'update_figure')]
    public function index(
        int $id,
        FigureRepository $figureRepository,
        Request $request,
        ValidatorInterface $validator,
        FileUploader $fileUploader,
        SluggerInterface $slugger
    ): Response {
        $figure = $figureRepository->findFigureById($id);

        if (!$figure) {
            $this->addFlash('danger', 'Figure non trouvée.');
            return $this->redirectToRoute('home');
        }

        $this->denyAccessUnlessGranted('FIGURE_EDIT', $figure);

        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $errors = $this->validateWithoutGalleryUrls($validator, $figure);

            if (count($errors) === 0) {
                try {
                    $mainMediaFile = $form->get('mainMediaFileUpload')->getData();
                    if ($mainMediaFile) {
                        try {
                            $mainMediaFileName = $fileUploader->upload($mainMediaFile);
                            $figure->setMainMedia('/uploads/figures/' . $mainMediaFileName);
                        } catch (\Exception $e) {
                            $this->addFlash('danger', 'Erreur lors de l\'upload du média principal : ' . $e->getMessage());
                            return $this->render('figure_update.html.twig', [
                                'figure' => $figure,
                                'form' => $form->createView(),
                            ]);
                        }
                    }

                    $mediaGalleryFiles = $form->get('mediaGalleryFileUpload')->getData();
                    if ($mediaGalleryFiles && count($mediaGalleryFiles) > 0) {
                        try {
                            $mediaFileNames = $fileUploader->uploadMultiple($mediaGalleryFiles);
                            $mediaGallery = $figure->getMediaGallery() ?: [];

                            foreach ($mediaFileNames as $fileName) {
                                $mediaGallery[] = '/uploads/figures/' . $fileName;
                            }

                            $figure->setMediaGallery($mediaGallery);
                        } catch (\Exception $e) {
                            $this->addFlash('danger', 'Erreur lors de l\'upload des médias de la galerie : ' . $e->getMessage());
                            return $this->render('figure_update.html.twig', [
                                'figure' => $figure,
                                'form' => $form->createView(),
                            ]);
                        }
                    }

                    $mediaGallery = array_filter($figure->getMediaGallery(), function($media) {
                        return !empty(trim($media));
                    });
                    $figure->setMediaGallery($mediaGallery);
                    $figure->setUpdatedAt(new \DateTimeImmutable());

                    $figureRepository->save($figure);

                    $this->addFlash('success', 'La figure a été modifiée avec succès !');

                    $slug = strtolower($slugger->slug($figure->getName())->toString());
                    return $this->redirectToRoute('figure', [
                        'id' => $figure->getId(),
                        'slug' => $slug
                    ]);
                } catch (UniqueConstraintViolationException $e) {
                    $this->addFlash('danger', 'Une figure avec ce nom existe déjà. Veuillez choisir un autre nom.');
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de la modification de la figure : ' . $e->getMessage());
                }
            } else {
                foreach ($errors as $error) {
                    $errorMessage = sprintf('%s : %s', $error->getPropertyPath(), $error->getMessage());
                    $this->addFlash('danger', $errorMessage);
                }
            }
        }

        return $this->render('figure_update.html.twig', [
            'figure' => $figure,
            'form' => $form->createView(),
        ]);
    }

    private function validateWithoutGalleryUrls(ValidatorInterface $validator, $figure)
    {
        $mediaGallery = $figure->getMediaGallery();
        $figure->setMediaGallery([]);
        $errors = $validator->validate($figure);
        $figure->setMediaGallery($mediaGallery);
        return $errors;
    }
}