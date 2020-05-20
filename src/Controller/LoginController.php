<?php

namespace App\Controller;

use App\Entity\Usuarios;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class LoginController extends AbstractController
{
     /**
     * @Route("/login", name="login") 
     */
    
    public function index(Request $datos, SessionInterface $session )
    {        
        if($_POST){

            $em = $this->getDoctrine()->getManager(); 

            $contrasena = md5($datos->request->get('passwor'));
            $mail = $datos->request->get('mail');

            // Registro de nuevos usuarios
            if($datos->request->get('registro')){

                $nombre = $datos->request->get('nombre');
                
                $usuario= new Usuarios();
                $usuario->setNombre($nombre);
                $usuario->setMail($mail);
                $usuario->setContrasena($contrasena);
                
                $em->persist($usuario);
                $em->flush();

                $session->set('tipo', 'usuario');
                $session->set('user',$existe);

                return $this->redirectToRoute('/perfil');
            }

            //Login para usuarios existentes
            else{

                if($datos->request->get('administrador')){
                    $existe=$em->getRepository(Administradores::class)->findOneBy(['mail'=> $mail]);

                    if($existe){
                        $pass=$existe->getContrasena();
                        
                        if($pass==$contrasena){
                            $session->set('tipo', 'administrador');
                            $session->set('user',$existe);
                        }
                    }   
                }
                else{
                    $existe=$em->getRepository(Usuarios::class)->findOneBy(['mail'=> $mail]);

                    if($existe){
                        $pass=$existe->getContrasena();
                        
                        if($pass==$contrasena){
                            $session->set('tipo', 'usuario');
                            $session->set('user',$existe);
                        }
                    }
                }
                return $this->redirectToRoute('perfil');
            }
        }

        //si es un GET
        return $this->render('login/index.html.twig');
    }


    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request, SessionInterface $session)
    {
        $session->invalidate();
        
        return $this->redirectToRoute('inicio');
    }
}
 // var_dump($session->get('tipo'));