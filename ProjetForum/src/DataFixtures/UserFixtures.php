<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class UserFixtures extends Fixture
{
    
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
         $this->passwordHasher = $passwordHasher;
    }
    
    public function load(ObjectManager $manager)
    {
        // on va créer 5 admins et 5 clients+gestionnaires
        for ($i = 0; $i < 5 ; $i++){
            $user = new User();
            $user->setEmail ("user".$i."@gmail.com"); // user1@gmail.com, user2@gmail.com etc....
            $user->setPassword($this->passwordHasher->hashPassword(
                 $user,
                 'lepassword'.$i // lepassword1, lepassword2, etc...
             ));
            $user->setNom("nom".$i);
            $user->setRoles(['ROLE_ADMIN']);

            
            $manager->persist ($user);
            // référence en mémoire utilisée par autre Fixture pour créer les rélations
            $this->addReference('user_' . $i, $user);

        }
        for ($i = 0; $i <= 5 ; $i++){
            $user = new User();
            $user->setEmail ("autreuser".$i."@gmail.com"); // user1@gmail.com, user2@gmail.com etc....
            $user->setPassword($this->passwordHasher->hashPassword(
                 $user,
                 'lepassword'.$i // lepassword1, lepassword2, etc...
             ));
            $user->setNom("nom".$i);
            $user->setRoles(['ROLE_CLIENT','ROLE_GESTIONNAIRE']);

            
            $manager->persist ($user);
            // référence en mémoire utilisée par autre Fixture pour créer les rélations
            $this->addReference('user_' . ($i + 5), $user);
        }


        $manager->flush();
    }
}
