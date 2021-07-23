<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public const USERS = [

        [
            'username' => 'MySuperAdmin',
            'roles' => ['ROLE_SUPER_ADMIN'],
            'password' => 'jesuisbientoutvabien'
        ],

        [
            'username' => 'MyAdmin',
            'roles' => ['ROLE_ADMIN'],
            'password' => 'ilfautmangerdespommes'
        ],
    ];

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        foreach(self::USERS as $key => $value) {
            $user = new User();
            $user->setUsername($value['username']);
            $user->setRoles($value['roles']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $value['password']));
            $manager->persist($user);
            $manager->flush();

        }

        $manager->flush();
    }
}
