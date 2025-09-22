<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


// importer les entities
use App\Entity\Animal;

final class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'app_accueil')]
    public function index(): Response
    {

        dd($this->getUser());


        $adresse = ['rue' => 'Rue Van Aa',
                    'numero' => 203,
                    'codePostal' => '1050'
        ];

        $vars = ['nom' => 'Jean', // passage de variable simple
                'hobby' => 'natation',
                'dateNaissance' => new \DateTime ("2000-1-6"), // passage d'un objet
                'adresse' => $adresse
        ]; 
        
        // dd ($vars);

        return $this->render('accueil/index.html.twig', $vars);

    }
    #[Route('/accueil/testModele')]
    public function testModele (EntityManagerInterface $em){
        
        // on va obtenir des entités de la BD
        // 1. obtenir le repo de l'entité 
        $rep = $em->getRepository(Animal::class);
        $arrayAnimaux = $rep->findAll();


        // $arrayAnimaux = $em->getRepository(Animal::class)->findAll();
        // dd($arrayAnimaux);

        $vars = [ 
            'animaux' => $arrayAnimaux
        ];

        return $this->render('accueil/test_modele.html.twig', $vars);

    }
}
