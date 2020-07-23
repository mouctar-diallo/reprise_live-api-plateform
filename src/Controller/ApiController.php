<?php

namespace App\Controller;

use App\Entity\Region;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\RegionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{

    /**
     * @Route("/api/regions_externe", name="region_externe",methods={"GET"})
     */
    public function getRegionsToApi(SerializerInterface $serialize,EntityManagerInterface $em)
    {
        $jsonRegions = file_get_contents("https://geo.api.gouv.fr/regions");

        $regions = $serialize->deserialize($jsonRegions,'App\Entity\Region[]','json');

        foreach ($regions as  $region) {
            $em->persist($region);
        }
        $em->flush();

        return new JsonResponse("success",201,[],true);
    }

    /**
     * @Route("/api/regions_bdd", name="region_bdd",methods={"GET"})
     */
    public function getRegionsToDatabase(SerializerInterface $serialize,EntityManagerInterface $em,RegionRepository $repo)
    {
        $regions = $repo->findAll();

        $converTojson = $serialize->serialize($regions,"json",['groups'=>'region:read']);

        return new JsonResponse($converTojson,200,[],true);
    }

    /**
     * @Route("/api/regions_add", name="region_add",methods={"POST"})
     */
    public function createRegion(Request $request,SerializerInterface $serialize, EntityManagerInterface $em,RegionRepository $repo,ValidatorInterface $validator)
    {
        $jsonRegion = $request->getContent();
        $region = $serialize->deserialize($jsonRegion,Region::class,'json');
    
       $erreurs = $validator->validate($region);
       if (count($erreurs)>0) {
            return new JsonResponse($erreurs ,Response::HTTP_BAD_REQUEST,[],true);
       }
       $em->persist($region);
       $em->flush();

       return new JsonResponse("created successfully",201);
    }

    // /**
    //  * @Route("/api/departement_add", name="departement_add",methods={"POST"})
    //  */
    // public function addDepartement(Request $request,SerializerInterface $serialize, EntityManagerInterface $em,RegionRepository $repo,ValidatorInterface $validator)
    // {
    //  
    //    return new JsonResponse("created successfully",201);
    // }
}
