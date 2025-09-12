<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Categorie;
use Faker;

class CategorieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create("fr-BE");
        
        for ($i = 0; $i < 5; $i++){
            $categorie = new Categorie();
            $categorie->setNom($faker->word());
            $categorie->setDescription($faker->text(100));
            $manager->persist($categorie);
        }

        $manager->flush();
    }
}
