<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    //pour une référence fixe, créer une constante
    public const ADMIN = 'ADMIN_USER';


    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    )
    {
        
    }


    public function load(ObjectManager $manager): void
    {
        //On crée un user avec un rôle admin
        $user = new User();
        $user->setRoles(['ROLE_ADMIN'])
            ->setEmail('admin@doe.fr')
            ->setUsername('admin')
            ->setVerified(true)
            ->setPassword($this->hasher->hashPassword($user, 'admin'))
            ->setApiToken('admin_token');
        //On crée une référence à la constante 
        //le nom est 'ADMIN_USER' et l'objet est $user défini ci-dessus
        $this->addReference(self::ADMIN, $user);
        $manager->persist($user);

        //On crée 10 User simples
        for ($i = 1; $i <= 10; $i++) {
            $user = (new User)
                ->setRoles([])
                ->setEmail("user{$i}@doe.fr")
                ->setUsername("user{$i}")
                ->setVerified(true)
                ->setPassword($this->hasher->hashPassword($user, '0000'))
                ->setApiToken("user{$i}");
            //on crée une référence pour les recettes de l'utilisateur
            //le nom est par exemple 'USER 4' et l'objet est $user défini ci-dessus
            $this->addReference("USER" . $i, $user);
            $manager->persist($user);
        }

        //On envoie le tout en bdd
        $manager->flush();
    }

}