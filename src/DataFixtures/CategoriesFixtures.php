<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoriesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");

        for ($i=0 ;$i<10 ; $i++){
            $categorie = new Categorie();
            $categorie->setLibelle($faker->word());

            $this->addReference("categorie".$i,$categorie);

            $manager->persist($categorie);
        }

        $manager->flush();
    }
}
