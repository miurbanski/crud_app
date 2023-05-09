<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Employee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $company1 = new Company();
        $company1->setName('Company 1');
        $company1->setNip('1234567890');
        $company1->setAddress('123 Main St');
        $company1->setCity('Anytown');
        $company1->setPostalCode('12345');;

        $manager->persist($company1);

        $employee1 = new Employee();
        $employee1->setFirstname('John');
        $employee1->setLastname('Doe');
        $employee1->setEmail('johndoe@example.com');
        $employee1->setTelephoneNumber('123-456-7890');
        $employee1->setCompany($company1);

        $manager->persist($employee1);

        $manager->flush();
    }
}
