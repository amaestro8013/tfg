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



//
// ADMINISTRADORES
//

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




//
// USUARIOS
//

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
            $mail = $datos->request->get('mail');

            $usuario=$em->getRepository(Usuarios::class)->find($id);

            $u=$em->getRepository(Usuarios::class)->findBy(['mail'=>$mail]);
            $contador = 0;
            foreach ($u as $existemail){
                if ($existemail->getMail() == $mail){
                    if($usuario->getMail() == $mail){
                    }else{
                        $contador = 1;
                    }
                }
            }
            if ($contador == 0){
                $contrasena = md5($datos->request->get('passwor'));
                $nombre = $datos->request->get('nombre');

                if( empty($datos->request->get('bloquear')) ) {
                    $bloqueado=false;
                }else{
                    $bloqueado=true;
                }

                $usuario->setNombre($nombre);
                $usuario->setMail($mail);
                $usuario->setBloqueado($bloqueado);
                if($contrasena == ''){
                }else{
                    $usuario->setContrasena($contrasena);
                }
                
                $em->persist($usuario);
                $em->flush();              
            }
            else{
                echo '<script>
                    alert("Email ya existe");
                    </script>';
            }              
        }
        return $this->redirectToRoute('gestion-usuarios');
    }




//
//CANCIONES
//
    /**
     * @Route("/gestion-canciones", name="gestion-canciones")
     */
    public function canciones()
    {
        $em = $this->getDoctrine()->getManager();

        $canciones=$em->getRepository(Canciones::class)->findAll();

        $relacionCancionAutor = array();
        $i = 0;
        foreach ($canciones as $cancion){

            $relacionCancionAutor[$i]=$em->getRepository(Cancionesautores::class)->findBy(['cancionesIdcanciones'=>$cancion->getIdcanciones()]);
            $i = $i +1;
        }

        return $this->render('administradores/canciones.html.twig', [
            'canciones' => $relacionCancionAutor,
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

            //guarda los autores para despues crear la etiqueta de la cancion
            $etiquetaCancion = array();
            $i=0;

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
                    $etiquetaAutor=$em->getRepository(Etiquetas::class)->findOneBy(['nombre'=>$autor]);
                    $cancionEtiqueta = new Cancionesetiquetas();
                    $cancionEtiqueta->setEtiquetasIdetiquetas($etiquetaAutor);
                    $cancionEtiqueta->setCancionesIdcanciones($cancion);

                    $em->persist($cancionEtiqueta);
                    $em->flush();

                    $etiquetaCancion[$i]=$cantante->getNombre();
                }
                else{
                    //creamos la etiqueta del autor
                    if(!$autor==''){
                        $newTagAutor= new Etiquetas();
                        $newTagAutor->setNombre($autor);
                        $newTagAutor->setTipo(1);

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

                        $etiquetaCancion[$i]=$autor;
                    }
                }
                $i=$i+1;
            }

            //creamos la etiqueta de la cancion
            $tituloYArtistas = $titulo;
            $tituloYArtistas .= ' - ';
            
            for ($i = 1; $i <= count($etiquetaCancion); $i++) {
                if($i == count($etiquetaCancion)){
                    $tituloYArtistas .= $etiquetaCancion[$i-1];
                }else{
                    $tituloYArtistas .= $etiquetaCancion[$i-1].', ';
                }
            }

            $newTagCancion= new Etiquetas();
            $newTagCancion->setNombre($tituloYArtistas);
            $newTagCancion->setTipo(2);

            $em->persist($newTagCancion);
            $em->flush();

            $cancionEtiqueta = new Cancionesetiquetas();
            $cancionEtiqueta->setEtiquetasIdetiquetas($newTagCancion);
            $cancionEtiqueta->setCancionesIdcanciones($cancion);

            $em->persist($cancionEtiqueta);
            $em->flush();

            
            //Etiquetas de generos y relacion con etiquetas
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
                
            //Etiquetas nuevas y relacion
            $newEtiquetas=$datos->request->get('newEtiqueta');
            if($newEtiquetas){
                foreach ($newEtiquetas as $newEtiqueta){
                    if(!$newEtiqueta==''){
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
        }

        $em = $this->getDoctrine()->getManager();
        $etiquetas=$em->getRepository(Etiquetas::class)->findBy(['tipo'=>0]);


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
 
        $autores=$this->autoresDeCancion($id);
        $etiquetasSeleccionadas=$this->etiquetasDeCancion($id);
        $etiquetasTodas=$em->getRepository(Etiquetas::class)->findBy(['tipo'=>0]);

        return $this->render('administradores/cancionesEditar.html.twig',[
            'cancion' => $cancion, 'autores' => $autores, 'etiquetas' => $etiquetasSeleccionadas, 'etiquetasTodas' => $etiquetasTodas,
        ]);
    }

    /**
     * @Route("/edit-cancion/", name="edit-cancion")
     */
    public function editCancion(Request $datos)
    {
        if($_POST){

            $em = $this->getDoctrine()->getManager(); 

            $id = $datos->request->get('id');
            
            $this->addCancion($datos);
            $this->eliminarCancion($id);
        }

        return $this->redirectToRoute('gestion-canciones');
    }

    public function autoresDeCancion($id){

        $em = $this->getDoctrine()->getManager();

        $cancionAutores=$em->getRepository(Cancionesautores::class)->findBy(['cancionesIdcanciones'=>$id]);
        
        $autores = array();
        $i = 0;
        foreach ($cancionAutores as $autorID){

            $autores[$i]=$em->getRepository(Autores::class)->findOneBy(['idautores'=>$autorID->getAutoresIdautores()]);
            $i = $i +1;
        }

        return $autores;
    }

    public function etiquetasDeCancion($id){

        $em = $this->getDoctrine()->getManager();

        $cancionEtiquetas=$em->getRepository(Cancionesetiquetas::class)->findBy(['cancionesIdcanciones'=>$id]);
        
        $etiquetas = array();
        $i = 0;
        foreach ($cancionEtiquetas as $etiquetaID){

            $tag=$em->getRepository(Etiquetas::class)->findOneBy(['idetiquetas'=>$etiquetaID->getEtiquetasIdetiquetas()]);
            if($tag->getTipo()==0){
                $etiquetas[$i]=$tag;
            }
            $i = $i +1;
        }

        return $etiquetas;
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

}
