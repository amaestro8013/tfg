<?php

namespace App\Controller;

use App\Entity\Usuarios;
use App\Entity\Administradores;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
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
            
            if($session->get('get')==1){
                $session->set('get', 0);
                //si es un POST ha fallado
                return $this->render('login/index.html.twig');
            }else{

                $em = $this->getDoctrine()->getManager(); 

                $contrasena = md5($datos->request->get('passwor'));
                $mail = $datos->request->get('mail');

                // Registro de nuevos usuarios
                if($datos->request->get('registro')){

                    //comprobar usuario ya existe
                    if(!$existe=$em->getRepository(Usuarios::class)->findOneBy(['mail'=> $mail])){

                        $nombre = $datos->request->get('nombre');
                        
                        $usuario= new Usuarios();
                        $usuario->setNombre($nombre);
                        $usuario->setMail($mail);
                        $usuario->setContrasena($contrasena);
                        
                        $em->persist($usuario);
                        $em->flush();

                        // Establecer y obtener atributos de sesión
                        $session->set('quien', 'usuario');
                        $session->set('user',$usuario);

                        return $this->redirectToRoute('perfil');
                    }
                    else{
                        echo '<script>
                            alert("Este correo ya pertenece a un usuario");
                        </script>';
                        $session->set('get', 1);
                    }

                    
                }

                //Login para usuarios existentes
                else{

                    if($datos->request->get('administrador')){
                        //inicio de sesion de administradores
                        if($existe=$em->getRepository(Administradores::class)->findOneBy(['mail'=> $mail])){
                            $pass=$existe->getContrasena();
                            
                            if($pass==$contrasena){

                                $session->set('quien', 'administrador');
                                $session->set('user',$existe);

                                return $this->redirectToRoute('administrador');
                            }
                            else{
                                echo '<script>
                                    alert("Usuario o contraseña incorrectos");
                                </script>';
                                
                            }
                        }   
                        else{
                            echo '<script>
                                alert("Usuario o contraseña incorrectos");
                            </script>';
                            
                        }              
                    }
                    else{
                        if($existe=$em->getRepository(Usuarios::class)->findOneBy(['mail'=> $mail])){

                            $pass=$existe->getContrasena();

                            if($pass==$contrasena){

                                // Establecer y obtener atributos de sesión
                                $session->set('quien', 'usuario');
                                $session->set('user',$existe);

                                return $this->redirectToRoute('perfil');
                            }
                            else{
                                echo '<script>
                                    alert("Usuario o contraseña incorrectos");
                                </script>';
                                
                            }
                        }
                        else{
                            echo '<script>
                                alert("Usuario o contraseña incorrectos");
                            </script>';
                            
                        }
                    }
                }
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
        $session->clear(); 
        $session->invalidate(); 
        $session->clear(); 
        
        
        return $this->redirectToRoute('inicio');
    }
}