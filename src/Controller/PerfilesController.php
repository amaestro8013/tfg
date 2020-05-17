<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PerfilesController extends AbstractController
{
    /**
     * @Route("/perfiles", name="perfiles")
     */
    public function index()
    {
        return $this->render('perfiles/index.html.twig', [
            'controller_name' => 'PerfilesController',
        ]);
    }
}
