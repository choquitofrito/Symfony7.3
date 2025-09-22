<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Proprietaire;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Animal;

final class AffichageController extends AbstractController
{
    #[Route('/affichage', name: 'app_affichage')]
    public function index(EntityManagerInterface $em, Request $req): Response
    {
        // on obtient côté 1 (Proprietaires)
        $rep = $em->getRepository(Proprietaire::class);
        $arrayProprietaires = $rep->findAll(); 

        

        $id = $req->get('id_proprietaire');
        
        // deux chemins possibles:

        $arrayAnimaux = [];

        // 1. on n'a pas cliqué sur le lien (dans l'url il y a juste /afficher)
        if (is_null($id)){
            // obtenir un ensemble aléatoire d'animaux
            $repAnimal = $em->getRepository (Animal::class);
            $arrayAnimaux = $repAnimal->findAll();
        }
        // 2. on a cliqué sur un lien (dans l'url y aura /afficher/id_proprietaire=3 - exemple)
        else {
            // obtenir les animaux du propriétaire
            $proprietaire = $rep->find($id); // obtenir le proprietaire à partir de l'id
            $arrayAnimaux = $proprietaire->getAnimaux();
        }
        $vars = ['proprietaires' => $arrayProprietaires,
                'animaux' => $arrayAnimaux];
        return $this->render('affichage/index.html.twig', $vars);
    }


    #[Route('/affichage/select', name: 'app_affichage_select')]
    public function affichageSelect (EntityManagerInterface $em, Request $req): Response
    {
        $rep = $em->getRepository(Proprietaire::class);
        $arrayProprietaires = $rep->findAll();


        // dd($arrayProprietaires);

        // obtenir l'id du proprietaire choisi dans le select
        $id = $req->get('id_proprietaire');

        if ($id==""){
            // obtenir un ensemble aléatoire d'animaux
            $repAnimal = $em->getRepository (Animal::class);
            $arrayAnimaux = $repAnimal->findAll();
        }
        // 2. on a cliqué sur un lien (dans l'url y aura /afficher/id_proprietaire=3 - exemple)
        else {
            // obtenir les animaux du propriétaire
            $proprietaire = $rep->find($id); // obtenir le proprietaire à partir de l'id
            $arrayAnimaux = $proprietaire->getAnimaux();
        }

        $vars = ['proprietaires' => $arrayProprietaires,
                'animaux' => $arrayAnimaux];

        return $this->render ('affichage/affichage_select.html.twig', $vars);
    }
}
