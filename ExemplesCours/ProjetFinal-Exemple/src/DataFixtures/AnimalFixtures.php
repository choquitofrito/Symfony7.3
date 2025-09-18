<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Animal;
use Faker;
use App\Entity\Proprietaire;
use App\DataFixtures\ProprietaireFixtures;
use App\Entity\Utilisateur;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AnimalFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager):void
    {
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $animal = new Animal();
            $animal->setNom($faker->name());


            // ex1: lien avec Proprietaire
            $unProprietaire = $this->getReference("proprietaire". rand(0,2), Proprietaire::class);
            $animal->setProprietaire($unProprietaire);
            
            // ex2: lien avec Utilisateur
            $animal->setPossedeur($this->getReference("utilisateur" . rand (1,5), Utilisateur::class));
            $manager->persist($animal);
        }
        $manager->flush();
    }

    public function getDependencies():array
    {
        return (
            [ProprietaireFixtures::class]
        );
    }
}