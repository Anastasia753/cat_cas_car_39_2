<?php

namespace App\DataFixtures;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixtures
{

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function loadData(ObjectManager $manager): void
    {

        $this->create(User::class, function (User $user) use ($manager) {
            $user
                ->setEmail('admin@catcascar.ru')
                ->setFirstName('Администратор')
                ->setPassword($this->passwordHasher->hashPassword($user, '123456'))
                ->setRoles(['ROLE_ADMIN'])
                ->setSubscribeToNewsletter(true)
            ;

            $manager->persist(new ApiToken($user));
        });

        $this->createMany(User::class, 10, function (User $user) use ($manager) {
            $user
                ->setEmail($this->faker->email)
                ->setFirstName($this->faker->firstName)
                ->setPassword($this->passwordHasher->hashPassword($user, '123456'))
                ->setSubscribeToNewsletter($this->faker->boolean())
            ;

            $manager->persist(new ApiToken($user));
        });

        $manager->flush();
    }
}
