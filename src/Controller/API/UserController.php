<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class UserController extends AbstractController
{
    #[Route('/api/me')]
    #[IsGranted('ROLE_USER')]
    public function me()
    {
        dd($this->getUser());
        return $this->json($this->getUser());
    }
}