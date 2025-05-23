<?php

namespace AlAya\Common\DataFixtures;

use AlAya\Common\Entity\Agent;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AgentFixture extends Fixture implements FixtureGroupInterface
{

    public function __construct(private UserPasswordHasherInterface $hasher)
    {

    }
    

    public function load(ObjectManager $manager): void
    {

        
        $user = $manager->getRepository(Agent::class)->findOneBy([ "username" => "admin" , "createdBy" => "system"]); 

        if (is_null($user)) {
            $user = new Agent() ;
        }

        $user->setUsername('admin');
        $user->setLastName("admin");
        $user->setFirstName("admin");
        $user->setEmail("admin@gmail.com");
        $user->setCreatedAt(new DateTimeImmutable("now"));
        $user->setCreatedBy("system");
        $user->setPassword($this->hasher->hashPassword($user, '123456'));

        $manager->persist($user);
     
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['Agent'];
    }

}
