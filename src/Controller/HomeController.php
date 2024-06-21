<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController{
    #[Route("/", name: "home")]
    function index(Request $request, EntityManagerInterface $em): Response
    {
        //code pour créer un utilisateur (injecter UserPasswordHasherInterface $hasher dans la signature)
/*         $user = new User;
        $user->setEmail('jane@doe.be')
            ->setUsername('Jaja')
            ->setPassword($hasher->hashPassword($user, '0000'))
            ->setRoles([]);
            $em->persist($user);
            $em->flush();
 */
        return $this->render('home/index.html.twig');
        
        // en utilisant une méthdoe de AbstractController
        // return $this->redirect();
        
        // sans utiliser AbstractController & ses méthodes

        // -en utilisant la Request (avec une valeur par défaut)
        //return new Response ('Hallo ' . $request->query->get('name', 'Janosch'));

        // -PHP classique en récupérant ?name=Janosch dans la variable globale
        //return new Response('Hallo ' . $_GET['name']);
    }
}