<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CancionesController extends AbstractController
{
    /**
     * @Route("/canciones", name="canciones")
     */
    public function index()
    {
        return $this->render('canciones/index.html.twig', [
            'controller_name' => 'CancionesController',
        ]);
    }
}
