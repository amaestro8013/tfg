<?php

namespace App\Controller;

use App\Entity\Etiquetas;
use App\Entity\Perfiles;
use App\Entity\Perfilesetiquetas;
use App\Entity\Canciones;
use App\Entity\Cancionesetiquetas;
use App\Entity\Probabilidades;
use App\Entity\Autores;
use App\Entity\Cancionesautores;


 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class InicioController extends AbstractController
{
    /**
     * @Route("/", name="inicio")
     */
    public function index()
    {

        $em = $this->getDoctrine()->getManager();
        
        $etiquetas=$em->getRepository(Etiquetas::class)->findBy(['tipo'=>0]);
        $autores=$em->getRepository(Etiquetas::class)->findBy(['tipo'=>1]);

        return $this->render('inicio/index.html.twig', [
            'etiquetas' => $etiquetas, 'autores' => $autores,
        ]);
    }

    /**
     * @Route("/perfil-musical", name="siguiente")
     */
    public function perfilAnonimo(Request $datos)
    {
        $em = $this->getDoctrine()->getManager();
        if($_POST){

            $etiquetas=$datos->request->get('etiqueta');
            if($etiquetas){
                
                $perfil = new Perfiles();
                $perfil->setNombre('NO REGISTRADO');
                $em->persist($perfil);
                $em->flush();
                
                $this->añadirEtiquetas($etiquetas, $perfil);

                $canciones=$this->seleccionaCanciones($perfil);

                return $this->render('inicio/canciones.html.twig', [
                    'canciones' => $canciones, 'perfil'=>$perfil
                ]);

            }else{
                echo '<script>type="text/javascript">
                    alert("Selecciona al menos un artista o genero musical");
                </script>';

                return $this->redirectToRoute('inicio');
            }
        }
        return $this->redirectToRoute('inicio');
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

    /**
     * @Route("/buscar", name="buscar")
     */
    public function busqueda( Request $datos)
    {
        $em = $this->getDoctrine()->getManager();

        //obtenemos el ID del perfil del que vamos a realizar la busqueda de canciones similares
        $id=$datos->request->get('perfilId');
        
        //obtenemos el perfil
        $perfil = $em->getRepository(Perfiles::class)->find($id);

        //si es la primera vez que se crea el perfil (petición de tipo POST)
        //añadimos las etiquetas de las canciones seleccionadas al perfil
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

    public function cancionesAMostrar($perfil){

        $em = $this->getDoctrine()->getManager();

        //obtenemos las probabilidades calculadas anteriormente, de este determinado perfil
        $probabilidadesDelPerfil = $em->getRepository(Probabilidades::class)->findBy(['idperfil'=>$perfil->getIdperfiles()]);

        $generos=array();
        $i=0;
        $artistas=array();
        $j=0;

        //dividimos las probabilidades en probabilidades de generos y probabilidades de artistas
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

        //inicializamos los arrays que almacenarán la información para despues calcular las canciones
        $mayorProbabilidades=array();
        $idGenero=array();
        $idArtista=array();

        $numCanciones=5;

        for($i = 1; $i <= $numCanciones; $i++){
            $mayorProbabilidades[$i]=0;
        }

        //multiplicamos cada probabilidad entre si y nos quedamos con las 5 mayores
        foreach ($generos as $g){
            foreach ($artistas as $a){
                $probabilidad= $g->getProbabilidad() * $a->getProbabilidad();

                for($i = 1; $i <= $numCanciones; $i++){
                    if ($mayorProbabilidades[$i] < $probabilidad){
                        $mayorProbabilidades[$i] = $probabilidad;
                        $idGenero[$i]= $g->getIdetiqueta();
                        $idArtista[$i]= $a->getIdetiqueta();
                    }
                }
            }   
        }

        $cancionesARecomendar=array();

        //para cada una de las 5 mayores probabilidades calculadas anteriormente
        for($i = 1; $i <= $numCanciones; $i++){

            //obtenemos las canciones cantadas por el artista seleccionado
            $autor=$em->getRepository(Autores::class)->findOneBy(['etiquetasIdetiquetas'=>$idArtista[$i]]);
            $cancionesAutor=$em->getRepository(Cancionesautores::class)->findBy(['autoresIdautores'=>$autor->getIdautores()]);

            ////obtenemos las canciones del genero seleccionado
            $cancionesGenero=$em->getRepository(Cancionesetiquetas::class)->findBy(['etiquetasIdetiquetas'=>$idGenero[$i]]);


            //almacenamos en un array las canciones que son del autor y el genero calculados anteriormente
            foreach ($cancionesAutor as $CA){
                foreach ($cancionesGenero as $CG){
                    if ($CA->getCancionesIdcanciones() == $CG->getCancionesIdcanciones()){
                        $cancionesARecomendar[$CA->getCancionesIdcanciones()->getIdcanciones()]=$CA->getCancionesIdcanciones();
                    }
                }
            }
        }


        //por ultimo, obtenemos la estiqueta de la cancion en concreto, por que hes mucho mas manejable en las plantillas
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

}
