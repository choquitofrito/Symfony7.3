<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CategorieProduitFixtures extends Fixture implements DependentFixtureInterface {

    public function load (ObjectManager $manager):void {
        // 1. obtenir toutes les Categories (tous les objets du côté 1 de la rélation)
        $repoCategorie = $manager->getRepository(Categorie::class);
        $toutesCategories = $repoCategorie->findAll();

        // 2. obtenir tous les Produits (tous les objets du côté n de la rélation)
        $repoProduit = $manager->getRepository(Produit::class);
        $tousProduits = $repoProduit->findAll();

        // 3. donner une Categorie à chaque Produit (parcourir tous les objets du côté n de la rélation et affecter
        // à chaque objet un objet aléatoire du côté 1) 
        foreach ($tousProduits as $produit){
            $produit->setCategorie ($toutesCategories[rand(0,count($toutesCategories)-1)]);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return (
            [
                ProduitFixtures::class,
                CategorieFixtures::class
            ]
            );
    }
}
