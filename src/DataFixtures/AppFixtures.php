<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Profil;
use App\Entity\Commune;
use App\Entity\Departement;
use App\Repository\RegionRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $regionRepo;
    private $encode;

    public function __construct(RegionRepository $regionRepo, UserPasswordEncoderInterface $encoder )
    {
        $this->regionRepo = $regionRepo;
        $this->encode = $encoder;
    }
    public function load(ObjectManager $manager)
    {
         $faker = Factory::create('fr_FR');
        $regions = $this->regionRepo->findAll();

        foreach ($regions as $region) {
            $departement = new Departement();
            $departement->setCode($faker->postcode)
                        ->setNom($faker->city)
                        ->setRegion($region);
            $manager->persist($departement);
            
            for ($i=0; $i < 10; $i++) { 
                $commune = new Commune();
                $commune->setCode($faker->postcode)
                        ->setCommune($faker->city)
                        ->setDepartement($departement);

                $manager->persist($commune);
            }
        }
        
        $manager->flush();
    }
}
