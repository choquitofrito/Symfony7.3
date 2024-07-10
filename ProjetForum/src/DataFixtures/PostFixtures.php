<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Post;
use App\DataFixtures\UserFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 20; $i++) {
            $post = new Post();
            $post->setTitre($faker->sentence);
            $post->setContenu($faker->paragraph);
            $post->setDateCreation($faker->dateTimeThisYear);
            // références en mémoire pour créer de rélations
            $post->setUser($this->getReference('user_' . rand(1, 5)));

            $manager->persist($post);

            $this->addReference('post_' . $i, $post);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
