<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ForosController extends AbstractController
{
    /**
     * @Route("/foros", name="foros")
     */
    public function index()
    {
        return $this->render('foros/index.html.twig', [
            'controller_name' => 'ForosController',
        ]);
    }
}
