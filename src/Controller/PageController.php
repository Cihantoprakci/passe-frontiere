<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    #[Route('/methode', name: 'method')]
    public function method(): Response
    {
        return $this->render('page/method.html.twig');
    }

    #[Route('/mentions-legales', name: 'legal_notice')]
    public function legalNotice(): Response
    {
        return $this->render('page/legal_notice.html.twig');
    }

    #[Route('/confidentialite', name: 'legal_privacy')]
    public function privacy(): Response
    {
        return $this->render('page/privacy.html.twig');
    }
}
