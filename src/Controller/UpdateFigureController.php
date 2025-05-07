<?php

namespace App\Controller;

use App\Form\FigureType;
use App\Repository\FigureRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateFigureController extends AbstractController
{
    #[Route('/update_figure/{id}', name: 'update_figure')]
    public function index(
        int $id,
        FigureRepository $figureRepository,
        Request $request,
        ValidatorInterface $validator
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
            if ($form->isValid()) {
                try {
                    $mediaGallery = array_filter($figure->getMediaGallery(), function($media) {
                        return !empty(trim($media));
                    });
                    $figure->setMediaGallery($mediaGallery);
                    $figure->setUpdatedAt(new \DateTimeImmutable());

                    $figureRepository->save($figure);

                    $this->addFlash('success', 'La figure a été modifiée avec succès !');
                    return $this->redirectToRoute('figure', [
                        'id' => $figure->getId(),
                        'slug' => $this->slugify($figure->getName())
                    ]);
                } catch (UniqueConstraintViolationException $e) {
                    $this->addFlash('danger', 'Une figure avec ce nom existe déjà. Veuillez choisir un autre nom.');
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de la modification de la figure.');
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

        return $this->render('figure_update.html.twig', [
            'figure' => $figure,
            'form' => $form->createView(),
        ]);
    }

    private function slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        return strtolower($text);
    }
}