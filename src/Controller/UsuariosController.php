<?php

namespace App\Controller;

use App\Entity\Usuarios;
use App\Entity\Perfiles;
use App\Entity\Etiquetas;
use App\Entity\Perfilesetiquetas;

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
     * @Route("/editar-perfil", name="editar-perfil")
     */
    public function editarPerfil(SessionInterface $session)
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
    public function editPerfil(SessionInterface $session, Request $datos)
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
                        alert("Contraseña Antigua incorrecta");
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
    public function eliminarUsuario($id)
    {
        $em = $this->getDoctrine()->getManager();

        $usuario=$em->getRepository(Usuarios::class)->find($id);

        $perfilesusuario=$em->getRepository(Perfiles::class)->findBy(['usuariosIdusuarios'=>$id]);
        
        foreach ($perfilesusuario as $perfil){

            $relacion=$em->getRepository(Perfilesetiquetas::class)->findBy(['perfilesIdperfiles'=>$pergil->getPerfilesIdperfiles()]);
            foreach ($relacion as $relacionPerfilEtiqueta){
                $em->remove($relacionPerfilEtiqueta);
            }
            $em->remove($perfil);
        }
        $em->flush();
        
        $em->remove($usuario);
        $em->flush();
        
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
            
            $id=$datos->request->get('idUsuario');

            $etiquetas=$datos->request->get('etiqueta');
            if($etiquetas){
                
                $perfil = new Perfiles();
                $perfil->setNombre($nombre);
                $em->persist($perfil);
                $perfil->setUsuariosIdusuarios($usuario);
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

        $etiquetas=$em->getRepository(Etiquetas::class)->findBy(['tipoautor'=>0]);
        $autores=$em->getRepository(Etiquetas::class)->findBy(['tipoautor'=>1]);

        return $this->render('perfil/addPerfilMusical.html.twig', [
            'etiquetas' => $etiquetas, 'autores' => $autores
        ]);
    }

    

    



}
