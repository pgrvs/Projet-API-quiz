<?php

namespace App\DataFixtures;

use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class QuestionsFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");


        for ($i = 0; $i < 100; $i++) {

            $question = new Question();
            $question->setLibelleQuestion($faker->paragraph(1) . " ?");

            $this->addReference("question" . $i, $question);

            $iCategorie = $faker->numberBetween(0,9);
            $question->setCategorie($this->getReference("categorie" . $iCategorie));

            $manager->persist($question);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoriesFixtures::class
        ];
    }
}
