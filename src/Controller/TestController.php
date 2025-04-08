<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test-mail')]
    public function testMail(MailerInterface $mailer, LoggerInterface $logger): Response
    {
        $email = (new Email())
            ->from($_ENV['MAILER_FROM'])
            ->to('enzomularczyk@gmail.com') //
            ->subject('TEST FINAL 3')
            ->text('ça marche');

        try {
            $mailer->send($email);
            $logger->info("Email envoyé à Mailtrap");
            return new Response("Email envoyé");
        } catch (\Exception $e) {
            $logger->error("ERREUR: ".$e->getMessage());
            return new Response("Échec: ".$e->getMessage());
        }
    }
}