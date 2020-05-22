<?php

namespace App\Controller;

use App\Entity\Usuarios;
use App\Entity\Autores;
use App\Entity\Canciones;
use App\Entity\Etiquetas;
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
     * @Route("/gestion-canciones", name="gestion-canciones")
     */
    public function canciones()
    {
        $em = $this->getDoctrine()->getManager();

        $canciones=$em->getRepository(Canciones::class)->findAll();

        return $this->render('administradores/canciones.html.twig', [
            'canciones' => $canciones,
        ]);
    }

    /**
     * @Route("/add-cancion", name="add-cancion")
     */
    public function addCancion(Request $datos)
    {

        if($_POST){

            $em = $this->getDoctrine()->getManager(); 

            $titulo = $datos->request->get('titulo');
            $duracion = $datos->request->get('duracion');
            $album = $datos->request->get('album');
            $fecha = $datos->request->get('fecha');

            $cancion= new Canciones();
            $cancion->setTitulo($titulo);
            $cancion->setDuracion($duracion);
            $cancion->setAlbum($album);
            $cancion->setFecha($fecha);

            $autores=$datos->request->get('autor'); 
            foreach ($autores as $autor){
                if($cantante=$em->getRepository(Autores::class)->findOneBy(['nombre'=> $autor])){

                    $cancion->addAutoresIdautore($cantante);
                }
                else{
                    $newCantante= new Autores();
                    $newCantante->setNombre($autor);

                    $em->persist($newCantante);
                    $em->flush();

                    $cancion->addAutoresIdautore($newCantante);
                }
            }
            
            $etiquetas=$datos->request->get('etiqueta');
            foreach ($etiquetas as $id){
                
                $etiqueta=$em->getRepository(Etiquetas::class)->find($id);
                $cancion->addEtiquetasIdetiqueta($etiqueta);
            }

            $newEtiquetas=$datos->request->get('newEtiqueta');
            foreach ($newEtiquetas as $newEtiqueta){
                
                $newTag= new Etiquetas();
                $newTag->setNombre($newEtiqueta);

                $em->persist($newTag);
                $em->flush();

                $cancion->addEtiquetasIdetiqueta($newTag);
            }

            $em->persist($cancion);
            $em->flush();
        }

        $em = $this->getDoctrine()->getManager();
        $etiquetas=$em->getRepository(Etiquetas::class)->findAll();


        return $this->render('administradores/cancionAdd.html.twig',[
            'etiquetas' => $etiquetas ,
        ]);
    }


    /**
     * @Route("/editar-cancion/{id}", name="editar-cancion")
     */
    public function editarCancion($id)
    {
        $em = $this->getDoctrine()->getManager();

        $cancion=$em->getRepository(Canciones::class)->find($id);
 

        $autores=$em->getRepository(Canciones::class)->findBy(['autoresIdautores'=>$id]);


        // $query = $em->createQuery('SELECT Autores_idAutores FROM Autores');
        // $autoresIds = $query->getResult();

        var_dump($autoresIds);die;

        return $this->render('administradores/cancionesEditar.html.twig',[
            'cancion' => $cancion,
        ]);
    }


    /**
     * @Route("/eliminar-cancion/{id}", name="eliminar-cancion")
     */
    public function eliminarCancion($id)
    {
        $em = $this->getDoctrine()->getManager();

        $cancion=$em->getRepository(Canciones::class)->find($id);
        
        $em->remove($cancion);
        $em->flush();
        
        return $this->redirectToRoute('gestion-canciones');
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

        return $this->render('administradores/usuarioEditar.html.twig',[
            'usuario' => $user,
        ]);
    }

    /**
     * @Route("/edit-user", name="edit-user")
     */
    public function editUser(Request $datos)
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
