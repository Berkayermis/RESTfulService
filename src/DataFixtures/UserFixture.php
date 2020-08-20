<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserFixture extends Fixture
{
    private $encoder ;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setPassword(
            $this->encoder->encodePassword($user,'0000')
        );

        $manager->persist($user);

        $user2 = new User();
        $user2->setUsername('user');
        $user2->setPassword(
            $this->encoder->encodePassword($user2,'user')
        );

        $manager->persist($user2);
        $manager->flush();
    }
}
