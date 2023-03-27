<?php

namespace App\DataFixtures;

use App\Entity\Reponse;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ReponseFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");

        for ($iQuestion=0; $iQuestion<100 ;$iQuestion++) {

            $numReponseJuste = $faker->numberBetween(0,3);

            for ($i = 0; $i < 4; $i++) {

                $reponse = new Reponse();
                $reponse->setLibelleReponse($faker->paragraph(1));

                $reponse->setQuestion($this->getReference("question" . $iQuestion));

                if ($i === $numReponseJuste){
                    $reponse->setIsTrue(true);
                } else {
                    $reponse->setIsTrue(false);
                }

                $manager->persist($reponse);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            QuestionsFixtures::class
        ];
    }
}
