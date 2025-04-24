<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Security\FigureVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class DeleteFigureController extends AbstractController
{
    #[Route('/figure/delete/{id}', name: 'trick_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Figure $figure,
        EntityManagerInterface $entityManager,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        $this->denyAccessUnlessGranted(FigureVoter::DELETE, $figure);

        $tokenId = 'delete_figure_' . $figure->getId();
        $submittedToken = $request->request->get('_token');

        if (!$csrfTokenManager->isTokenValid(new CsrfToken($tokenId, $submittedToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide !');
        }

        $entityManager->remove($figure);
        $entityManager->flush();

        $this->addFlash('success', 'Figure supprimée avec succès');
        return $this->redirectToRoute('home');
    }
}