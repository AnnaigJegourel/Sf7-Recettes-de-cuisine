<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    )
    {
        
    }


    public function load(ObjectManager $manager): void
    {
        $user = new User();

        //On crée un user avec un rôle admin
        $user->setRoles(['ROLE_ADMIN'])
            ->setEmail('admin@doe.fr')
            ->setUsername('admin')
            ->setVerified(true)
            ->setPassword($this->hasher->hashPassword($user, 'admin'))
            ->setApiToken('admin_token');
        $manager->persist($user);

        //On crée 10 User simples
        for ($i = 1; $i <= 10; $i++) {
            $user->setRoles([])
                ->setEmail('user{$i}@doe.fr')
                ->setUsername('user{$i}')
                ->setVerified(true)
                ->setPassword($this->hasher->hashPassword($user, '0000'))
                ->setApiToken('user{$i}');
            $manager->persist($user);
        }

        //On envoie le tout en bdd
        $manager->flush();
    }
}
