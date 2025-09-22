<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


// importer les entities
use App\Entity\Animal;
use App\Entity\Proprietaire;

final class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'app_accueil')]
    public function index(EntityManagerInterface $em): Response
    {

        // obtenir tous le éléments du Côté Propriétaire (côté 1)
        $rep= $em->getRepository(Proprietaire::class);
        $arrayProprietaires = $rep->findAll();

        $vars = ['proprietaires' => $arrayProprietaires];

       
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
