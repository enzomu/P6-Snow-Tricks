<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\FigureType;
use App\Repository\FigureRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateFigureController extends AbstractController
{
    #[Route('/create_figure', name: 'create_figure')]
    public function create(
        Request $request,
        FigureRepository $figureRepository,
        ValidatorInterface $validator
    ): Response {
        $this->denyAccessUnlessGranted('FIGURE_CREATE');

        $figure = new Figure();
        $figure->setMediaGallery([]);

        $user = $this->getUser();
        $figure->setAuthor($user);

        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $mediaGallery = array_filter($figure->getMediaGallery(), function($media) {
                        return !empty(trim($media));
                    });
                    $figure->setMediaGallery($mediaGallery);
                    $figure->setCreatedAt(new \DateTimeImmutable());
                    $figureRepository->save($figure);

                    $this->addFlash('success', 'La figure a été créée avec succès !');
                    return $this->redirectToRoute('home');
                } catch (UniqueConstraintViolationException $e) {
                    $this->addFlash('danger', 'Une figure avec ce nom existe déjà. Veuillez choisir un autre nom.');
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de la création de la figure.');
                }
            } else {
                $errors = $validator->validate($figure);
                foreach ($errors as $error) {
                    $errorMessage = sprintf('%s : %s', $error->getPropertyPath(), $error->getMessage());
                    $this->addFlash('danger', $errorMessage);
                }

                if (count($errors) === 0) {
                    $this->addFlash('danger', 'Veuillez vérifier les informations saisies.');
                }
            }
        }

        return $this->render('figure_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}