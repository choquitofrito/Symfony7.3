<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class IngredientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $ingredient = new Ingredient();
            // si on a un hydrate, pas besoin de sets...
            $ingredient->setNom("ingredient" . $i);
           
           
            $manager->persist($ingredient);
    }
    $manager->flush();
}
}
