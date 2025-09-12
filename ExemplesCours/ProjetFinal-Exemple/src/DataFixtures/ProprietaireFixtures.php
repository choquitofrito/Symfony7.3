<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Proprietaire;
use Faker;

class ProprietaireFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 3; $i++) {
            $proprietaire = new Proprietaire();
            $proprietaire->setNom($faker->name());
            $proprietaire->setEmail($faker->email());
            $manager->persist($proprietaire);

            $this->addReference("proprietaire" . $i, $proprietaire );

        }
        // proprietaire0
        // proprietaire1
        // proprietaire2

        $manager->flush();
    }
}