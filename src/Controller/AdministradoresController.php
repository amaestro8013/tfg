<?php

namespace App\Controller;

use App\Entity\Usuarios;
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

        return $this->render('administradores/administradores.html.twig', [
            'administradores' => $administradores,
        ]);
    }

        /**
     * @Route("/gestion-usuarios", name="gestion-usuarios")
     */
    public function usuarios()
    {
        $em = $this->getDoctrine()->getManager();

        $usuarios=$em->getRepository(Usuarios::class)->findAll();

        return $this->render('administradores/usuarios.html.twig', [
            'usuarios' => $usuarios,
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


    /**
     * @Route("/editar-usuario/{id}", name="editar-usuario")
     */
    public function editarUsuario($id)
    {
        $em = $this->getDoctrine()->getManager();

        $user=$em->getRepository(Usuarios::class)->find($id);

        return $this->render('administradores/editarUsuario.html.twig',[
            'usuario' => $user,
        ]);
    }

    /**
     * @Route("/edit-user", name="edit-user")
     */
    public function esitUser(Request $datos)
    {
        if($_POST){
            $em = $this->getDoctrine()->getManager();

            $id=$datos->request->get('id');
            $nombre = $datos->request->get('nombre');
            $mail = $datos->request->get('mail');
            $contrasena = md5($datos->request->get('passwor'));
            
            if( empty($datos->request->get('bloquear')) ) {
                $bloqueado=false;
            }else{
                $bloqueado=true;
            }

            $usuario=$em->getRepository(Usuarios::class)->find($id);
            
            $usuario->setNombre($nombre);
            $usuario->setMail($mail);
            $usuario->setContrasena($contrasena);
            $usuario->setBloqueado($bloqueado);

            

            $em->persist($usuario);
            $em->flush();
        }
        return $this->redirectToRoute('gestion-usuarios');


    }
 
    
}
