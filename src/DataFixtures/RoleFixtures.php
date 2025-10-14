<?php

namespace App\DataFixtures;

use App\Entity\Configuration;
use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $roles = ['utilisateur', 'admin','visiteur','employe'];

        foreach ($roles as $libelle) {
            $role = new Role();
            $role->setLibelle($libelle);
            $manager->persist($role);
        }

        $configuration = new Configuration();
        $manager->persist($configuration);

        $manager->flush();
    }
}
