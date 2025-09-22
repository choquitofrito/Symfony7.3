<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Proprietaire;

final class AnimalController extends AbstractController
{
    #[Route('/animal/afficher/{id_proprietaire}', name: 'app_animal_afficher')]
    public function afficher_animaux(Request $req, EntityManagerInterface $em): Response
    {

        $id_proprietaire = $req->get('id_proprietaire');
        $rep = $em->getRepository(Proprietaire::class);
        $proprietaire = $rep->find ($id_proprietaire); // obtenir un certain propriétaire
        $arrayAnimaux = $proprietaire->getAnimaux();
        
        $vars = ['animaux' => $arrayAnimaux];


        return $this->render('animal/afficher_animaux.html.twig', $vars);
    }
}
