<?php

namespace App\Controller;

use App\Entity\Usuarios;
use App\Entity\Perfiles;
use App\Entity\Etiquetas;
use App\Entity\Foros;
use App\Entity\Perfilesetiquetas;
use App\Entity\Forosetiquetas;
use App\Entity\Cancionesetiquetas;
use App\Entity\Probabilidades;
use App\Entity\Mensajes;
use App\Entity\Autores;
use App\Entity\Cancionesautores;



use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UsuariosController extends AbstractController
{
    /**
     * @Route("/perfil", name="perfil")
     */
    public function index(SessionInterface $session)
    {
        $user=$session->get('user');

        $em = $this->getDoctrine()->getManager();

        $perfiles=$em->getRepository(Perfiles::class)->findBy(['usuariosIdusuarios' => $user->getIdusuarios()]);

        return $this->render('perfil/index.html.twig', [
            'perfiles' => $perfiles,
        ]);
    }

    /**
     * @Route("/get-perfil/{id}", name="get-perfil/")
     */
    public function getPerfilMusical(Request $datos, SessionInterface $session, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $perfil=$em->getRepository(Perfiles::class)->find($id);

        $etiquetas=$em->getRepository(Perfilesetiquetas::class)->findBy(['perfilesIdperfiles'=>$perfil]);

        $canciones = $this->cancionesAMostrar($perfil);

        return $this->render('perfil/perfilMusical.html.twig', [
            'perfil' => $perfil, 'etiquetas' => $etiquetas, 'canciones' => $canciones,
        ]);
    }

    /**
     * @Route("/editar-perfil-musical/{id}", name="editar-perfil-musical")
     */
    public function editarPerfilMusical(Request $datos, SessionInterface $session, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $perfil=$em->getRepository(Perfiles::class)->find($id);

        $seleccionadas=$em->getRepository(Perfilesetiquetas::class)->findBy(['perfilesIdperfiles'=>$perfil]);

        $etiquetas=$em->getRepository(Etiquetas::class)->findBy(['tipo'=>0]);
        $autores=$em->getRepository(Etiquetas::class)->findBy(['tipo'=>1]);
        

        return $this->render('perfil/editarPerfilMusical.html.twig', [
            'perfil' => $perfil, 'seleccionadas' => $seleccionadas, 'etiquetas' => $etiquetas,
             'autores' => $autores, 
        ]);
    }

     /**
     * @Route("/edit-perfil-musical", name="edit-perfil-musical")
     */
    public function editPerfilMusical(Request $datos, SessionInterface $session)
    {
        if($_POST){

            $em = $this->getDoctrine()->getManager(); 

            $id = $datos->request->get('idPerfil');

            $this->eliminarPerfilMusical($id);

            

            $id=$datos->request->get('idUsuario');
            $usuario= $em->getRepository(Usuarios::class)->find($id);
            $nombre=$datos->request->get('nombre');
            $em->persist($usuario);

            $etiquetas=$datos->request->get('etiqueta');
            if($etiquetas){
                
                $perfil = new Perfiles();
                $perfil->setNombre($nombre);
                $em->persist($perfil);
                $perfil->setUsuariosIdusuarios($usuario);
                $em->persist($perfil);
                $em->flush();
                
                $this->añadirEtiquetas($etiquetas, $perfil);
                $canciones=$this->seleccionaCanciones($perfil);


                //crear foro si existe o si no asociarlo a este perfil
                $this->foro($perfil ,$etiquetas);

                $canciones=$this->seleccionaCanciones($perfil);

                return $this->render('perfil/canciones.html.twig', [
                    'canciones' => $canciones, 'perfil'=>$perfil,
                ]);

            }else{
                echo '<script>type="text/javascript">
                    alert("Selecciona al menos un artista o genero musical");
                </script>';

                return $this->redirectToRoute('perfil');
            }

        }
        return $this->redirectToRoute('perfil');
    }

    public function foro($perfil, $etiquetas){
        $em = $this->getDoctrine()->getManager(); 

        $relacionEtiquetasPerfil=$em->getRepository(Perfilesetiquetas::class)->findBy(['perfilesIdperfiles'=>$perfil->getIdperfiles()]);
        $numRelacionEtiquetasPerfil = count($relacionEtiquetasPerfil);
        
        
        $existe=0;

        $foros=$em->getRepository(Foros::class)->findAll();

        if($foros){
            foreach ($foros as $foro){
                $relacionEtiquetasForo=$em->getRepository(Forosetiquetas::class)->findBy(['forosIdforos'=>$foro->getIdforos()]);
                $numRelacionEtiquetasForo = count($relacionEtiquetasForo);

                $contador=0;

                if ($numRelacionEtiquetasPerfil == $numRelacionEtiquetasForo){

                    foreach ($relacionEtiquetasForo as $etiquetaForo){
                        $F=$em->getRepository(Etiquetas::class)->find($etiquetaForo->getEtiquetasIdetiquetas());

                        foreach ($relacionEtiquetasPerfil as $etiquetaPerfil){
                            $P=$em->getRepository(Etiquetas::class)->find($etiquetaPerfil->getEtiquetasIdetiquetas());
        
                            if( $P->getIdetiquetas()==$F->getIdetiquetas() ){
                                $contador = $contador +1;
                            }
                        }
                    }
                    
                    if ($contador == $numRelacionEtiquetasForo){
                        $existe=1;
                        $perfil->setForosIdforos($foro);
                        $em->persist($perfil);
                        $em->flush();

                        
                    }else{
                        $contador = 0;
                    }
                }
            }
            if ($existe==0){
                $foro = new Foros();
                $em->persist($foro);
                $em->flush();

                $perfil->setForosIdforos($foro);
                $em->persist($perfil);
                $em->flush();

                foreach ($etiquetas as $id){
                    
                    $etiqueta=$em->getRepository(Etiquetas::class)->find($id);

                    $ForosEtiquetas = new Forosetiquetas();
                    $ForosEtiquetas->setEtiquetasIdetiquetas($etiqueta);
                    $ForosEtiquetas->setForosIdforos($foro);

                    $em->persist($ForosEtiquetas);
                    $em->flush();
                }
            }
        }else{
            $foro = new Foros();
            $em->persist($foro);
            $em->flush();

            $perfil->setForosIdforos($foro);
            $em->persist($perfil);
            $em->flush();

            foreach ($etiquetas as $id){
                
                $etiqueta=$em->getRepository(Etiquetas::class)->find($id);

                $ForosEtiquetas = new Forosetiquetas();
                $ForosEtiquetas->setEtiquetasIdetiquetas($etiqueta);
                $ForosEtiquetas->setForosIdforos($foro);

                $em->persist($ForosEtiquetas);
                $em->flush();
            }
        }
    }

    /**
     * @Route("/eliminar-perfil-musical/{id}", name="eliminar-perfil-musical")
     */
    public function eliminarPerfilMusical($id)
    {
        $em = $this->getDoctrine()->getManager(); 

        $perfil=$em->getRepository(Perfiles::class)->find($id);

        $relaciones=$em->getRepository(Perfilesetiquetas::class)->findBy(['perfilesIdperfiles'=>$perfil->getIdperfiles()]);

        foreach ($relaciones as $relacion){
            $em->remove($relacion);
            $em->flush();
        }
        $em->remove($perfil);
        $em->flush();
        
        return $this->redirectToRoute('perfil');
    }

    /**
     * @Route("/editar-perfil", name="editar-perfil")
     */
    public function editarPerfilUsuario(SessionInterface $session)
    {
   
        $em = $this->getDoctrine()->getManager();

        $usuario=$session->get('user');

        return $this->render('perfil/editarUsuario.html.twig', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * @Route("/edit-perfil", name="edit-perfil")
     */
    public function editPerfilUsuario(SessionInterface $session, Request $datos)
    {
   
        if($_POST){
            
            
            $em = $this->getDoctrine()->getManager();
            
            //cambiar datos del perfil
            if($datos->request->get('tipo')=='perfil'){

                $id=$datos->request->get('id');
                $usuario=$em->getRepository(Usuarios::class)->find($id);

                $mail = $datos->request->get('mail');
                
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
                    
                    if($contrasena == $usuario->getContrasena()){
                        $nombre = $datos->request->get('nombre');

                        $usuario->setNombre($nombre);
                        $usuario->setMail($mail);

                        $em->persist($usuario);
                        $em->flush();
                        
                        $session->set('user',$usuario);

                        return $this->redirectToRoute('perfil');
                    }
                    else{
                        echo '<script>
                        alert("Contraseña incorrecta");
                        </script>';
                    }
                }
                else{
                    echo '<script>
                        alert("Email ya existe");
                        </script>';
                }              
            }
            //cambiar contraseña del perfil
            else{
                $id=$datos->request->get('id');
                $usuario=$em->getRepository(Usuarios::class)->find($id);

                $contrasena = md5($datos->request->get('passwor'));
                    
                if($contrasena == $usuario->getContrasena()){
                    $new = md5($datos->request->get('newpasswor'));
                    $repeat = md5($datos->request->get('repeatpasswor'));

                    if($new == $repeat){
                        $usuario->setContrasena($new);
                        $em->persist($usuario);
                        $em->flush();

                        return $this->redirectToRoute('perfil');
                    }
                    else{
                        echo '<script>
                        alert("Las contraseñas no coinciden");
                        </script>';
                    }
                }else{
                    echo '<script>
                        alert("Contraseña incorrecta");
                        </script>';
                }
            }
        }

        $usuario=$session->get('user');        
        
        return $this->render('perfil/editarUsuario.html.twig', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * @Route("/eliminar-usuario/{id}", name="eliminar-usuario")
     */
    public function eliminarUsuario($id, SessionInterface $session)
    {
        $em = $this->getDoctrine()->getManager();

        $usuario=$em->getRepository(Usuarios::class)->find($id);

        $perfilesusuario=$em->getRepository(Perfiles::class)->findBy(['usuariosIdusuarios'=>$id]);
        
        foreach ($perfilesusuario as $perfil){
            
            $relacion=$em->getRepository(Perfilesetiquetas::class)->findBy(['perfilesIdperfiles'=>$perfil->getIdperfiles()]);
            foreach ($relacion as $relacionPerfilEtiqueta){
                $em->remove($relacionPerfilEtiqueta);
            }
            $em->remove($perfil);
        }
        $em->flush();
        
        $mensajes=$em->getRepository(Mensajes::class)->findBy(['usuariosIdusuarios'=>$id]);

        foreach ($mensajes as $mensaje){
            $em->remove($mensaje);
        }
        $em->flush();

        $em->remove($usuario);
        $em->flush();
        
        $session->clear(); 
        $session->invalidate(); 
        $session->clear(); 

        return $this->redirectToRoute('inicio');
    }
    
    /**
     * @Route("/add-perfil-musical", name="add-perfil-musical")
     */
    public function addPerfilMusical(Request $datos, SessionInterface $session)
    {
        $em = $this->getDoctrine()->getManager();
        
        if($_POST){

            $id=$datos->request->get('idUsuario');
            $usuario= $em->getRepository(Usuarios::class)->find($id);
            $nombre=$datos->request->get('nombre');
            $em->persist($usuario);

            $etiquetas=$datos->request->get('etiqueta');
            if($etiquetas){
                
                $perfil = new Perfiles();
                $perfil->setNombre($nombre);
                $em->persist($perfil);
                $perfil->setUsuariosIdusuarios($usuario);
                $em->persist($perfil);
                $em->flush();
                
                $this->añadirEtiquetas($etiquetas, $perfil);

                $this->foro($perfil, $etiquetas);
                

                

                $canciones=$this->seleccionaCanciones($perfil);

                return $this->render('perfil/canciones.html.twig', [
                    'canciones' => $canciones, 'perfil'=>$perfil,
                ]);

            }else{
                echo '<script>type="text/javascript">
                    alert("Selecciona al menos un artista o genero musical");
                </script>';

                return $this->redirectToRoute('perfil');
            }

            
        }

        $etiquetas=$em->getRepository(Etiquetas::class)->findBy(['tipo'=>0]);
        $autores=$em->getRepository(Etiquetas::class)->findBy(['tipo'=>1]);

        return $this->render('perfil/addPerfilMusical.html.twig', [
            'etiquetas' => $etiquetas, 'autores' => $autores,
        ]);
    }


    public function añadirEtiquetas($etiquetas, $perfil)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($etiquetas as $id){
            
            $etiqueta=$em->getRepository(Etiquetas::class)->find($id);

            $PerfilEtiquetas = new Perfilesetiquetas();
            $PerfilEtiquetas->setEtiquetasIdetiquetas($etiqueta);
            $PerfilEtiquetas->setPerfilesIdperfiles($perfil);

            $em->persist($PerfilEtiquetas);
            $em->flush();
        }
    }

    public function seleccionaCanciones($perfil)
    {
        $em = $this->getDoctrine()->getManager();

        $canciones=$em->getRepository(Etiquetas::class)->findBy(['tipo'=>2]);

        $etiquetasPerfil=$em->getRepository(PerfilesEtiquetas::class)->findBy(['perfilesIdperfiles'=>$perfil->getIdperfiles()]);

        $aMostrar=array();
        foreach ($canciones as $etiquetaCancion){

            $relacion=$em->getRepository(Cancionesetiquetas::class)->findBy(['etiquetasIdetiquetas'=>$etiquetaCancion->getIdetiquetas()]);

            foreach ($relacion as $r){
                $etiquetasCancion=$em->getRepository(Cancionesetiquetas::class)->findBy(['cancionesIdcanciones'=>$r->getCancionesIdcanciones()]);
            

                foreach ($etiquetasPerfil as $etiqueta){
                    foreach ($etiquetasCancion as $cancion){

                        if ($cancion->getEtiquetasIdetiquetas() == $etiqueta->getEtiquetasIdetiquetas()){
                            $aMostrar[$etiquetaCancion->getIdetiquetas()]=$etiquetaCancion;
                            
                        }
                    }
                }
            }
        }

        return $aMostrar;
    }

    /**
     * @Route("/buscar", name="buscar")
     */
    public function busqueda( Request $datos)
    {
        $em = $this->getDoctrine()->getManager();

        $id=$datos->request->get('perfilId');
        
        $perfil = $em->getRepository(Perfiles::class)->find($id);

        //añadimos las etiquetas de las canciones al perfil
        if($_POST){
            $etiquetas=$datos->request->get('etiqueta');
            if($etiquetas){
                
                $this->añadirEtiquetas($etiquetas, $perfil);

                $this->calcularProbabilidadesPerfil($perfil);

                $canciones = $this->cancionesAMostrar($perfil);

                return $this->render('inicio/busqueda.html.twig', [
                    'canciones' => $canciones,
                ]);
                
            }else{
                echo '<script>type="text/javascript">
                    alert("Selecciona al menos una canción");
                </script>';
                return $this->redirectToRoute('inicio');
            }
        }
         
        return $this->redirectToRoute('inicio');
    }



    public function calcularProbabilidadesPerfil($perfil)
    {
        $em = $this->getDoctrine()->getManager();

        //obtenemos las canciones que le gustan al usuario a partir del perfil
        $etiquetasDelPerfil=$em->getRepository(Perfilesetiquetas::class)->findBy(['perfilesIdperfiles'=>$perfil->getIdperfiles()]);
        
        $cancionesQueLeGustan=array();
        $i=0;
        foreach ($etiquetasDelPerfil as $e){
            
            $etiqueta=$em->getRepository(Etiquetas::class)->find($e->getEtiquetasIdetiquetas());
            if($etiqueta->getTipo()==2){
                $cancionesQueLeGustan[$i]=$etiqueta;
                $i = $i +1;
            }
        }
        $numCancionesQueLeGustan = count($cancionesQueLeGustan);


        //Obtenemos los generos para crear las probabilidades de este perfil
        $generos = $em->getRepository(Etiquetas::class)->findBy(['tipo'=>0]);

        foreach ($generos as $genero){
            $numCancionesPorGenero=0;

            foreach ($cancionesQueLeGustan as $c){

                $relacionEtiquetaCancion = $em->getRepository(Cancionesetiquetas::class)->findOneBy(['etiquetasIdetiquetas'=>$c->getIdetiquetas()]);
                //var_dump($relacionEtiquetaCancion);die;

                $generosDeLaCancion=$this->generosDeCancion($relacionEtiquetaCancion->getCancionesIdcanciones());
                
                foreach ($generosDeLaCancion as $generoDeCancion){
                    if($genero == $generoDeCancion){
                        $numCancionesPorGenero=$numCancionesPorGenero +1;;
                    }
                }
            }
            $probabilidades = new Probabilidades();
            $probabilidades->setIdperfil($perfil->getIdperfiles());
            $probabilidades->setIdetiqueta($genero->getIdetiquetas());
            $probabilidades->setTipo(0);
            $probabilidades->setProbabilidad($numCancionesPorGenero/$numCancionesQueLeGustan);

            $em->persist($probabilidades);
            $em->flush();
            
        }
        //Obtenemos los artistas para crear las probabilidades de este perfil
        $artistas = $em->getRepository(Etiquetas::class)->findBy(['tipo'=>1]);

        foreach ($artistas as $artista){
            $numCancionesPorArtista=0;

            foreach ($cancionesQueLeGustan as $c){

                $relacionEtiquetaCancion = $em->getRepository(Cancionesetiquetas::class)->findOneBy(['etiquetasIdetiquetas'=>$c->getIdetiquetas()]);
                //var_dump($relacionEtiquetaCancion);die;

                $artistasDeLaCancion=$this->artistasDeCancion($relacionEtiquetaCancion->getCancionesIdcanciones());
                
                foreach ($artistasDeLaCancion as $artistaDeCancion){
                    if($artista == $artistaDeCancion){
                        $numCancionesPorArtista=$numCancionesPorArtista +1;;
                    }
                }
            }
            $probabilidades = new Probabilidades();
            $probabilidades->setIdperfil($perfil->getIdperfiles());
            $probabilidades->setIdetiqueta($artista->getIdetiquetas());
            $probabilidades->setTipo(1);
            $probabilidades->setProbabilidad($numCancionesPorArtista/$numCancionesQueLeGustan);

            $em->persist($probabilidades);
            $em->flush();

        }
    }

    public function generosDeCancion($id){

        $em = $this->getDoctrine()->getManager();

        $cancionEtiquetas=$em->getRepository(Cancionesetiquetas::class)->findBy(['cancionesIdcanciones'=>$id]);
        
        $etiquetas = array();
        $i = 0;
        foreach ($cancionEtiquetas as $etiquetaID){

            $tag=$em->getRepository(Etiquetas::class)->findOneBy(['idetiquetas'=>$etiquetaID->getEtiquetasIdetiquetas()]);
            if($tag->getTipo()==0){
                $etiquetas[$i]=$tag;
                $i = $i +1;
            }  
        }

        return $etiquetas;
    }

    public function artistasDeCancion($id){

        $em = $this->getDoctrine()->getManager();

        $cancionEtiquetas=$em->getRepository(Cancionesetiquetas::class)->findBy(['cancionesIdcanciones'=>$id]);
        
        $etiquetas = array();
        $i = 0;
        foreach ($cancionEtiquetas as $etiquetaID){

            $tag=$em->getRepository(Etiquetas::class)->findOneBy(['idetiquetas'=>$etiquetaID->getEtiquetasIdetiquetas()]);
            if($tag->getTipo()==1){
                $etiquetas[$i]=$tag;
                $i = $i +1;
            }  
        }
        return $etiquetas;
    }


    public function cancionesAMostrar($perfil){

        $em = $this->getDoctrine()->getManager();

        $probabilidadesDelPerfil = $em->getRepository(Probabilidades::class)->findBy(['idperfil'=>$perfil->getIdperfiles()]);

        $generos=array();
        $i=0;
        $artistas=array();
        $j=0;

        foreach ($probabilidadesDelPerfil as $p){
            if ($p->getTipo()==0){
                $generos[$i]=$p;
                $i=$i+1;
            }
            elseif ($p->getTipo()==1){
                $artistas[$j]=$p;
                $j=$j+1;
            }
        }

        $mayorProbabilidades=array();
        $idGenero=array();
        $idArtista=array();

        $numCanciones=5;

        for($i = 1; $i <= $numCanciones; $i++){
            $mayorProbabilidades[$i]=0;
        }

        foreach ($generos as $g){
            foreach ($artistas as $a){
                $probabilidad= $g->getProbabilidad() * $a->getProbabilidad();

                for($i = 1; $i <= $numCanciones; $i++){
                    if ($mayorProbabilidades[$i] < $probabilidad){
                        $mayorProbabilidades[$i] = $probabilidad;
                        $idGenero[$i]= $g->getIdetiqueta();
                        $idArtista[$i]=$a->getIdetiqueta();
                    }
                }
            }   
        }
        

        $cancionesARecomendar=array();

        for($i = 1; $i <= $numCanciones; $i++){

            $autor=$em->getRepository(Autores::class)->findOneBy(['etiquetasIdetiquetas'=>$idArtista[$i]]);
            $cancionesAutor=$em->getRepository(Cancionesautores::class)->findBy(['autoresIdautores'=>$autor->getIdautores()]);

            $cancionesGenero=$em->getRepository(Cancionesetiquetas::class)->findBy(['etiquetasIdetiquetas'=>$idGenero[$i]]);

            foreach ($cancionesAutor as $CA){
                foreach ($cancionesGenero as $CG){
                    if ($CA->getCancionesIdcanciones() == $CG->getCancionesIdcanciones()){
                        $cancionesARecomendar[$CA->getCancionesIdcanciones()->getIdcanciones()]=$CA->getCancionesIdcanciones();
                    }
                }
            }
        }

        $canciones=array();
        
        foreach ($cancionesARecomendar as $cancion){
            
            $etiquetasDeLaCancion = $em->getRepository(Cancionesetiquetas::class)->findBy(['cancionesIdcanciones'=>$cancion->getIdcanciones()]);

            foreach ($etiquetasDeLaCancion as $e){
                $etiqueta = $em->getRepository(Etiquetas::class)->find($e->getEtiquetasIdetiquetas());

                if ($etiqueta->getTipo()==2){
                    $canciones[$etiqueta->getIdetiquetas()]=$etiqueta;
                }
    
            }
        }

        return $canciones;

        
    }


    /**
     * @Route("/foro/{id}", name="foro{id}")
     */
    public function getForo($id)
    {
        $em = $this->getDoctrine()->getManager();

        $perfil=$em->getRepository(Perfiles::class)->find($id);

        $foro=$em->getRepository(Foros::class)->find($perfil->getForosIdforos());        

        $foroEtiquetas=$em->getRepository(Forosetiquetas::class)->findBy(['forosIdforos'=>$perfil->getForosIdforos()]);

        $etiquetas= array();
        $i = 0;
        foreach ($foroEtiquetas as $tag){
            $etiquetas[$i] = $em->getRepository(Etiquetas::class)->find(['idetiquetas'=>$tag->getEtiquetasIdetiquetas()]);
            $i = $i +1;
        }

        $mensajes=$em->getRepository(Mensajes::class)->findBy(['forosIdforos'=>$foro->getIdforos()]);

        return $this->render('perfil/foro.html.twig', [
            'perfil' => $perfil, 'foro' => $foro, 'etiquetas'=>$etiquetas, 'mensajes'=>$mensajes,
        ]);
    }

    /**
     * @Route("/foro", name="foro")
     */
    public function newComment(Request $datos, SessionInterface $session)
    {
        $em = $this->getDoctrine()->getManager();

        $comentario=$datos->request->get('comentario');
        
        $foroId=$datos->request->get('idForo');
        $foro=$em->getRepository(Foros::class)->find($foroId);        

        $perfilId=$datos->request->get('idPerfil');
        $perfil=$em->getRepository(Perfiles::class)->find($perfilId);
        $usuario=$em->getRepository(Usuarios::class)->find($perfil->getUsuariosIdusuarios());
        
        if($usuario->getBloqueado()==0){
            $mensaje = new Mensajes();
            $mensaje->setComentario($comentario);
            $mensaje->setFecha(date('Y-m-d H:i:s'));
            $mensaje->setForosIdforos($foro);
            $mensaje->setUsuariosIdusuarios($usuario);

            $em->persist($mensaje);
            $em->flush();
        }else{
            echo '<script>type="text/javascript">
                    alert("No puede escribir mensajes, esta bloquedado en el sistema");
                </script>';
        }
        
        
        $foroEtiquetas=$em->getRepository(Forosetiquetas::class)->findBy(['forosIdforos'=>$perfil->getForosIdforos()]);
        $etiquetas= array();
        $i = 0;
        foreach ($foroEtiquetas as $tag){
            $etiquetas[$i] = $em->getRepository(Etiquetas::class)->find(['idetiquetas'=>$tag->getEtiquetasIdetiquetas()]);
            $i = $i +1;
        }

        $mensajes=$em->getRepository(Mensajes::class)->findBy(['forosIdforos'=>$foro->getIdforos()]);

        return $this->render('perfil/foro.html.twig', [
            'perfil' => $perfil, 'foro' => $foro, 'etiquetas'=>$etiquetas, 'mensajes'=>$mensajes,
        ]);
    }

    



}
