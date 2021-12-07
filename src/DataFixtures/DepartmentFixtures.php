<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Department;

class DepartmentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $departments= [
            1 => [
                'name' => 'Direction',
                'manager_mail' => 'manager-direction@test.fr'
            ],
            2 => [
                'name' => 'Ressources Humaines',
                'manager_mail' => 'manager-rh@test.fr'
            ],
            3 => [
                'name' => 'Communication',
                'manager_mail' => 'manager-com@test.fr'
            ],
            4 => [
                'name' => 'Dévelopment',
                'manager_mail' => 'manager-dev@test.fr'
            ],
            5 => [
                'name' => 'Marketing',
                'manager_mail' => 'manager-marketing@test.fr'
            ],
            6 => [
                'name' => 'Service Client / Support',
                'manager_mail' => 'manager-support@test.fr'
            ],
            7 => [
                'name' => 'Production',
                'manager_mail' => 'manager-production@test.fr'
            ],
        ];

        foreach($departments as $key => $value) {
            $department = new Department();
            $department->setName($value['name']);
            $department->setManagerMail($value['manager_mail']);
            $manager->persist($department);

            $this->addReference('department_' . $key, $department);
        }

        $manager->flush();
    }
}
