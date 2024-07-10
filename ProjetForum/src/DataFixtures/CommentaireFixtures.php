<?php

namespace App\DataFixtures;

use App\Entity\Commentaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class CommentaireFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 500; $i++) {
            $commentaire = new Commentaire();
            $commentaire->setContenu($faker->sentence);
            $commentaire->setDateCreation($faker->dateTimeThisYear);
            // références en mémoire pour créer de rélations
            $commentaire->setPost($this->getReference('post_' . rand(1, 20)));
            $commentaire->setUser($this->getReference('user_' . rand(1, 10)));

            $manager->persist($commentaire);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            PostFixtures::class,
        ];
    }
}
