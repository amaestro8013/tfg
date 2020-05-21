<?php

namespace App\Controller;

use App\Entity\Administradores;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdministradoresController extends AbstractController
{
    /**
     * @Route("/administrador", name="administrador")
     */
    public function index()
    {
        return $this->render('administradores/index.html.twig');
    }

    /**
     * @Route("/gestion-administradores", name="gestion-administradores")
     */
    public function administradores()
    {
        $em = $this->getDoctrine()->getManager();

        $administradores=$em->getRepository(Administradores::class)->findAll();
        // var_dump($administradores);die;

        return $this->render('administradores/administradores.html.twig', [
            'administradores' => $administradores,
        ]);
    }

    /**
     * @Route("/eliminar-admin/{id}", name="eliminar-admin")
     */
    public function eliminarAdmin($id)
    {
        $em = $this->getDoctrine()->getManager();

        $administrador=$em->getRepository(Administradores::class)->find($id);
        
        $em->remove($administrador);
        $em->flush();
        

        return $this->redirectToRoute('gestion-administradores');
    }

    /**
     * @Route("/add-administrador", name="add-administrador")
     */
    public function addAdmin(Request $datos)
    {

        if($_POST){

            $em = $this->getDoctrine()->getManager(); 

            $contrasena = md5($datos->request->get('passwor'));
            $mail = $datos->request->get('mail');

            //comprobar admin ya existe
            if(!$existe=$em->getRepository(Administradores::class)->findOneBy(['mail'=> $mail])){
                
                $admin= new Administradores();
                $admin->setMail($mail);
                $admin->setContrasena($contrasena);
                
                $em->persist($admin);
                $em->flush();

            }
            else{
                echo '<script>type="text/javascript">
                    alert("Este correo ya pertenece a un administrador");
                </script>';
            }
        }

        return $this->render('administradores/add.html.twig');
    }


    
}
