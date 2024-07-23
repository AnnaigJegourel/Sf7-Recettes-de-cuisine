<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController{
    //#[Route("/{_locale}", name: "home")]
    #[Route("/", name: "home")]
    function index(TranslatorInterface $translator): Response
    {
        $translator->trans('Love U');

        return $this->render('home/index.html.twig');
    }
}