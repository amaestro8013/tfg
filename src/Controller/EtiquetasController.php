<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class EtiquetasController extends AbstractController
{
    /**
     * @Route("/etiquetas", name="etiquetas")
     */
    public function index()
    {
        return $this->render('etiquetas/index.html.twig', [
            'controller_name' => 'EtiquetasController',
        ]);
    }
}
