<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Form\AnimalType;

final class FormsController extends AbstractController
{
    #[Route('/afficher/form', name: 'app_forms')]
    public function afficherForm(): Response
    {
        // créer un objet formulaire
        $formAnimal = $this->createForm(AnimalType::class);


        $vars = ['formAnimal' => $formAnimal];
        
        // faire le rendu de la vue. Envoyer l'objet form
        // depuis le controller
        return $this->render('forms/afficher_form.html.twig', $vars);
    }
}
