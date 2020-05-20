<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdministradoresController extends AbstractController
{
    /**
     * @Route("/administrador", name="administrador")
     */
    public function index()
    {
        return $this->render('administradores/index.html.twig', [
            'controller_name' => 'AdministradoresController',
        ]);
    }
}
