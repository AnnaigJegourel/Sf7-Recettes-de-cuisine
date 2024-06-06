<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController{
    #[Route("/", name: "home")]
    function index(Request $request): Response {

        // en utilisant une méthdoe de AbstractController
        // return $this->redirect();
        
        // sans utiliser AbstractController & ses méthodes
        // -en utilisant la Request (avec une valeur par défaut)
        return new Response ('Hallo ' . $request->query->get('name', 'Janosch'));
        // -PHP classique en récupérant ?name=Janosch dans la variable globale
        //return new Response('Hallo ' . $_GET['name']);
    }
}