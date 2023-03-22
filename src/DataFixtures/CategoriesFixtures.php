<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{

    private SluggerInterface $slugger;

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");

        for ($i=0 ;$i<10 ; $i++){
            $categorie = new Categorie();
            $categorie->setLibelle($faker->word());
            $categorie->setSlug($this->slugger->slug($categorie->getLibelle())->lower());

            $this->addReference("categorie".$i,$categorie);

            $manager->persist($categorie);
        }

        $manager->flush();
    }
}
