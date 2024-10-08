<?php

namespace App\DataFixtures;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Finder\Finder;

use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;


// Cette fixture lancera tous les fichiers sql qui se trouvent dans DataFixtures/sql
// Utile si vous voulez lancez du SQL fixe en dehors des fixtures standards

// Pour créer les fichiers, faites export (enlevez création de tables etc... ce qui compte ce sont les inserts)
class CustomFixtures extends Fixture
{
    private $em;
 
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function load(ObjectManager $manager):void
    {
        $finder = new Finder();
 
        $finder->files()->in('src/DataFixtures/sql');
 
        $cnx = $this->em->getConnection();
 
        foreach ($finder as $file){
            $content = $file->getContents();
            $cnx->setAutoCommit(false);
            $cnx->executeStatement($content);
        }
        $manager->flush();
    
    }

}





