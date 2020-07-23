<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApprenantController extends AbstractController
{
    //la route est definit dans routes.yaml
    public function getApprenants(UserRepository $repo)
    {
        $formateurs = $repo->findByProfil("FORMATEUR");

        return $this->json($formateurs,Response::HTTP_OK);
    }
}
