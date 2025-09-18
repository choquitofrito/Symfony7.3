<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\Request;

use App\Form\AnimalType;
use App\Entity\Animal;
use Doctrine\ORM\EntityManagerInterface;

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


    #[Route('/forms/insert/animal', name:'app_forms_insert_animal')]
    public function insertAnimal(Request $req, EntityManagerInterface $em):Response{
        $animal = new Animal();

        $formAnimal = $this->createForm (AnimalType::class, $animal);

        $formAnimal->handleRequest($req);
        // ici on peut avoir deux situations différentes

        // 1. Formulaire rempli et posté
        if ($formAnimal->isSubmitted()){
            // on stocke l'objet dans la BD
            $em->persist ($animal);
            $em->flush();
            return $this->redirectToRoute ('app_form_afficher_animaux');
        }
        // 2. On ne vient pas d'un submit, alors on affiche tout simplement le form
        else {
            $vars = ['formAnimal' => $formAnimal];
            return $this->render ('forms/affiche_form_insert_animal.html.twig', $vars);
        }
    }

    #[Route('/forms/afficher/animaux', name:'app_form_afficher_animaux')]
    public function afficherAnimaux (EntityManagerInterface $em){
        // Envoyer à une vue tous les animaux de la BD
        // La vue les affichera
        $rep = $em->getRepository(Animal::class);
        $arrayAnimaux = $rep->findAll();

        $vars = ['animaux' => $arrayAnimaux];
        return $this->render ('forms/afficher_animaux.html.twig', $vars);
    }

    


}
