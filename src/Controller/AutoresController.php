<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AutoresController extends AbstractController
{
    /**
     * @Route("/autores", name="autores")
     */
    public function index()
    {
        return $this->render('autores/index.html.twig', [
            'controller_name' => 'AutoresController',
        ]);
    }
}
