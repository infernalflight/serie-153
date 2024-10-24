<?php

namespace App\DataFixtures;

use App\Entity\Serie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = \Faker\Factory::create('fr_FR');

        for ($i=0; $i<200; $i++) {
            $serie = new Serie();

            $serie->setName($faker->words(7, true))
                ->setGenres($faker->randomElement(['Drama', 'Comedy', 'Thriller', 'SF', 'Gore']))
                ->setStatus($faker->randomElement(['ended', 'returning', 'canceled']))
                ->setVote($faker->randomFloat(1,0,10))
                ->setPopularity($faker->randomFloat(2,0,1000))
                ->setFirstAirDate($faker->dateTimeBetween('-10 years'))
                ->setDateCreated(new \DateTime())
            ;

            if (\in_array($serie->getStatus(), ['ended', 'canceled'])) {
                $serie->setLastAirDate($faker->dateTimeBetween($serie->getFirstAirDate()));
            }

            $manager->persist($serie);
        }

        $manager->flush();
    }
}
