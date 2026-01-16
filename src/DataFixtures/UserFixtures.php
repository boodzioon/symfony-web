<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$name, $lastName, $email, $password, $apiKey, $roles]) {
            $user = new User;
            $user->setName($name);
            $user->setLastName($lastName);
            $user->setEmail($email);
            $user->setPassword($this->passwordEncoder->hashPassword($user, $password));
            $user->setVimeoApiKey($apiKey);
            $user->setRoles($roles);
            $manager->persist($user);
        }

        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            ['John', 'Wayne', 'jw@symf8.loc', 'pass', 'hjd8ehdh', ['ROLE_ADMIN']],
            ['John', 'Wayne2', 'jw2@symf8.loc', 'pass', 'hjd8ehdh', ['ROLE_ADMIN']],
            ['Bogusław', 'Nowakowski', 'bn@symf8.loc', 'pass', null, ['ROLE_USER']],
        ];
    }
}
