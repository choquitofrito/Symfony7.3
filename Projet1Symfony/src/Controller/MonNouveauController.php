<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MonNouveauController extends AbstractController
{
    #[Route('/mon/nouveau', name: 'app_mon_nouveau')]
    public function index(): Response
    {
        return $this->render('mon_nouveau/index.html.twig', [
            'controller_name' => 'MonNouveauController',
        ]);
    }
}
