<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MensajesController extends AbstractController
{
    /**
     * @Route("/mensajes", name="mensajes")
     */
    public function index()
    {
        return $this->render('mensajes/index.html.twig', [
            'controller_name' => 'MensajesController',
        ]);
    }
}
