<?php

namespace App\Controller;

use App\Entity\Etiquetas;
use App\Entity\Perfiles;
use App\Entity\Perfilesetiquetas;

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
    
        $etiquetas=$em->getRepository(Etiquetas::class)->findBy(['tipoautor'=>0]);
        $autores=$em->getRepository(Etiquetas::class)->findBy(['tipoautor'=>1]);

        return $this->render('inicio/index.html.twig', [
            'etiquetas' => $etiquetas, 'autores' => $autores
        ]);
    }

    /**
     * @Route("/buscar", name="buscar")
     */
    public function buscar()
    {

        $em = $this->getDoctrine()->getManager();
        
        if($_POST){

            $etiquetas=$datos->request->get('etiqueta');
            if($etiquetas){
                
                $perfil = new Perfiles();
                $perfil->setNombre('NO REGISTRADO');
                $em->persist($perfil);
                $em->flush();
                
                foreach ($etiquetas as $id){
                    
                    $etiqueta=$em->getRepository(Etiquetas::class)->find($id);

                    $PerfilEtiquetas = new Perfilesetiquetas();
                    $PerfilEtiquetas->setEtiquetasIdetiquetas($etiqueta);
                    $PerfilEtiquetas->setPerfilesIdperfiles($perfil);

                    $em->persist($PerfilEtiquetas);
                    $em->flush();
                }
            }else{
                echo '<script>type="text/javascript">
                    alert("Selecciona al menos un artista o genero musical");
                </script>';
            }
        }


        $this->busqueda($perfil) ;       

        

        return $this->render('inicio/busqueda.html.twig', [
            'canciones' => $canciones,
        ]);
    }
}
