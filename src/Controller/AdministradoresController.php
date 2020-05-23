<?php

namespace App\Controller;

use App\Entity\Usuarios;
use App\Entity\Autores;
use App\Entity\Canciones;
use App\Entity\Cancionesautores;
use App\Entity\Cancionesetiquetas;
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


    //CANCIONES

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

            $em->persist($cancion);
            $em->flush();

            //Autores y relacion con autores
            $autores=$datos->request->get('autor'); 
            foreach ($autores as $autor){
                if($cantante=$em->getRepository(Autores::class)->findOneBy(['nombre'=> $autor])){

                    //relacionar la cancion con el autor
                    $cancionAutor = new Cancionesautores();
                    $cancionAutor->setAutoresIdautores($cantante);
                    $cancionAutor->setCancionesIdcanciones($cancion);

                    $em->persist($cancionAutor);
                    $em->flush();

                    //relacionar la cancion con la etiqueta del autor
                    $etiquetaAutor=$em->getRepository(Etiquetas::class)->findBy(['nombre'=>$cantante]);
                    $cancionEtiqueta = new Cancionesetiquetas();
                    $cancionEtiqueta->setEtiquetasIdetiquetas($etiquetaAutor);
                    $cancionEtiqueta->setCancionesIdcanciones($cancion);

                    $em->persist($cancionEtiqueta);
                    $em->flush();
                }
                else{
                    //creamos la etiqueta del autor
                    $newTagAutor= new Etiquetas();
                    $newTagAutor->setNombre($autor);
                    $newTagAutor->setTipoautor(true);

                    $em->persist($newTagAutor);
                    $em->flush();

                    //creamos al autor con su etiqueta
                    $newCantante= new Autores();
                    $newCantante->setNombre($autor);
                    $newCantante->setEtiquetasIdetiquetas($newTagAutor);

                    $em->persist($newCantante);
                    $em->flush();

                    //relacionar la cancion con el autor
                    $cancionAutor = new Cancionesautores();
                    $cancionAutor->setAutoresIdautores($newCantante);
                    $cancionAutor->setCancionesIdcanciones($cancion);

                    $em->persist($cancionAutor);
                    $em->flush();

                    //relacionar la cancion con la etiqueta del autor
                    $cancionEtiqueta = new Cancionesetiquetas();
                    $cancionEtiqueta->setEtiquetasIdetiquetas($newTagAutor);
                    $cancionEtiqueta->setCancionesIdcanciones($cancion);

                    $em->persist($cancionEtiqueta);
                    $em->flush();
                }
            }
            
            //Etiquetas y relacion con etiquetas
            $etiquetas=$datos->request->get('etiqueta');
            if($etiquetas){
                foreach ($etiquetas as $id){
                    
                    $etiqueta=$em->getRepository(Etiquetas::class)->find($id);

                    $cancionEtiqueta = new Cancionesetiquetas();
                    $cancionEtiqueta->setEtiquetasIdetiquetas($etiqueta);
                    $cancionEtiqueta->setCancionesIdcanciones($cancion);

                    $em->persist($cancionEtiqueta);
                    $em->flush();
                }
            }
                

            $newEtiquetas=$datos->request->get('newEtiqueta');
            if($newEtiquetas){
                foreach ($newEtiquetas as $newEtiqueta){
                    
                    $newTag= new Etiquetas();
                    $newTag->setNombre($newEtiqueta);

                    $em->persist($newTag);
                    $em->flush();

                    $cancionNewEtiqueta = new Cancionesetiquetas();
                    $cancionNewEtiqueta->setEtiquetasIdetiquetas($newTag);
                    $cancionNewEtiqueta->setCancionesIdcanciones($cancion);

                    $em->persist($cancionNewEtiqueta);
                    $em->flush();
                }
            }
        }

        $em = $this->getDoctrine()->getManager();
        $etiquetas=$em->getRepository(Etiquetas::class)->findBy(['tipoautor'=>0]);


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
 
        $autores=$em->getRepository(Cancionesautores::class)->findBy(['autoresIdautores'=>$id]);


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

        $cancionAutor=$em->getRepository(Cancionesautores::class)->findBy(['cancionesIdcanciones'=>$id]);
        $cancionEtiqueta=$em->getRepository(Cancionesetiquetas::class)->findBy(['cancionesIdcanciones'=>$id]);
        
        foreach ($cancionAutor as $relacionAutor){
            $em->remove($relacionAutor);
        }
        foreach ($cancionEtiqueta as $relacionEtiqueta){
            $em->remove($relacionEtiqueta);
        }
        $em->flush();

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
