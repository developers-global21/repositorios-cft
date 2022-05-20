<?php

namespace App\Controller;

use App\Entity\Subproceso;
use App\Form\SubprocesoType;
use App\Repository\SubprocesoRepository;

use App\Entity\Subcategoria;
use App\Form\SubcategoriaType;
use App\Repository\SubcategoriaRepository;

use App\Entity\Categoria;
use App\Form\CategoriaType;
use App\Repository\CategoriaRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use DateTimeInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/subproceso")
 */
class SubprocesoController extends AbstractController
{
    /**
     * @Route("/", name="app_subproceso_index", methods={"GET"})
     */
    public function index(SubprocesoRepository $subprocesoRepository): Response
    {
        $subprocesos = $subprocesoRepository->findAll();
        $server_name = $_SERVER['SERVER_NAME'];
        $puerto = $_SERVER['SERVER_PORT'];
        $server_name = $_SERVER['SERVER_NAME'];
        switch ($puerto) {
            case "80":
                $rutaServidor = "http://" . $server_name . "/";
                break;
            case "8000":
                $rutaServidor = "http://" . $server_name . ":" . $puerto . "/";
                break;
            case "443":
                $rutaServidor = "https://" . $server_name .  "/";
                break;
        }
        $direccion_finala = $rutaServidor;
        $directorio = $this->getParameter('registros');
        $url_final = str_replace($directorio, $direccion_finala, $directorio) . "assets/archivos/";
        return $this->render('subproceso/index.html.twig', [
            'subprocesos' => $subprocesos,
            'url_final' => $url_final,
            'directorio' => $directorio,
        ]);
    }

    /**
     * @Route("/new", name="app_subproceso_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ManagerRegistry $doctrine, CategoriaRepository $categoriaRepository): Response
    {
        $categorias = $categoriaRepository->findAll();
        return $this->renderForm('subproceso/new.html.twig', [
            'categorias' => $categorias,
        ]);
    }

    /**
     * @Route("/save_subproceso", name="save_subproceso", methods={"POST"})
     */
    public function saveSubproceso(
        Request $request,
        SluggerInterface $slugger,
        ManagerRegistry $doctrine,
        CategoriaRepository $categoriaRepository,
        SubcategoriaRepository $subcategoriaRepository
    ): Response {
        $params = $request->request->all();

        $params = $request->request->all();
        $nombre = strtoupper(trim($params['nombre']));
        $categoriaId = intval($params['categoria']);
        $subcategoriaId = intval($params['subcategoria']);
        //buscamos la categoria
        $categoria = $categoriaRepository->find($categoriaId);
        if (!is_null($categoria)) {
            // determinamos el directgorio de la categpria
            $directorioCategoria = $categoria->getDirectorio();
            // buscamos la subcategoria
            $subcategoria = $subcategoriaRepository->find($subcategoriaId);
            if (!is_null($subcategoria)) {
                // determinamos el directorio de la subcategoria
                $directorioSubcategoria = $subcategoria->getDirectorio();
                // determinamos el directorio del subproceso
                $directorioSubproceso =  $directorioSubcategoria . '/' . $slugger->slug($nombre) . "-" . uniqid();
                $subpproceso = new Subproceso();
                $subpproceso->setNombre($nombre);
                $subpproceso->setDirectorio($directorioSubproceso);
                $subpproceso->setCategoria($categoria);
                $subpproceso->setSubcategoria($subcategoria);
                $entityManager = $doctrine->getManager();
                $entityManager->persist($subpproceso);
                $entityManager->flush();
                //--- creamos el directorio 
                $filesystem = new Filesystem();
                $filesystem->mkdir($directorioSubproceso, 0777);
                $filesystem->copy($this->getParameter('registros') . '/index.php', $directorioSubproceso . '/index.php');
                $filesystem->copy($this->getParameter('registros') . '/busca_procesos.php', $directorioSubproceso . '/busca_procesos.php');
                $filesystem->copy($this->getParameter('registros') . '/busca_subprocesos.php', $directorioSubproceso . '/busca_subprocesos.php');
                $filesystem->copy($this->getParameter('registros') . '/conexion.php', $directorioSubproceso . '/conexion.php');
                $filesystem->chmod($directorioSubproceso . '/index.php', 0777);
                $filesystem->chmod($directorioSubproceso . '/busca_procesos.php', 0777);
                $filesystem->chmod($directorioSubproceso . '/busca_subprocesos.php', 0777);
                $filesystem->chmod($directorioSubproceso . '/conexion.php', 0777);

                $estado = '1';
            } else {
                $estado = '0';
            }
        } else {
            $estado = '0';
        }

        $salida = array($estado);
        $response = new Response(json_encode($salida));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/{id}", name="app_subproceso_show", methods={"GET"})
     */
    public function show(
        Subproceso $subproceso,
        CategoriaRepository $categoriaRepository,
        SubcategoriaRepository $subcategoriaRepository
    ): Response {
        $categorias = $categoriaRepository->findAll();
        $subcategorias = $subcategoriaRepository->findAll();
        return $this->render('subproceso/show.html.twig', [
            'subproceso' => $subproceso,
            'categorias' => $categorias,
            'subcategorias' => $subcategorias,
        ]);
    }

    /**
     * @Route("/search_subproceso", name="search_subproceso", methods={"GET","POST"})
     */
    public function searchSubProceso(Request $request, SubcategoriaRepository $subcategoriaRepository): Response
    {
        $params = $request->request->all();
        $idProceso = $params['id'];

        $url = $this->generateUrl('subproceso_show', [
            'id' => $idProceso,
        ]);
        $respuesta = new Response($url);
        return $respuesta;
    }

    /**
     * @Route("/{id}", name="subproceso_show", methods={"GET","POST"})
     */
    public function showSubProceso(
        Request $request,
        SubprocesoRepository $subprocesoRepository,
        CategoriaRepository $categoriaRepository,
        SubcategoriaRepository $subcategoriaRepository
    ): Response {
        $idProceso = intval($request->query->get('id'));
        $subproceso = $subprocesoRepository->find($idProceso);
        $categorias = $categoriaRepository->findAll();
        $subcategorias = $subcategoriaRepository->findAll();
        return $this->render('subproceso/show.html.twig', [
            'subproceso' => $subproceso,
            'categorias' => $categorias,
            'subcategorias' => $subcategorias,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_subproceso_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Subproceso $subproceso, CategoriaRepository $categoriaRepository, SubcategoriaRepository $subcategoriaRepository): Response
    {
        $categorias = $categoriaRepository->findAll();
        $subcategorias = $subcategoriaRepository->findAll();
        return $this->renderForm('subproceso/edit.html.twig', [
            'subproceso' => $subproceso,
            'categorias' => $categorias,
            'subcategorias' => $subcategorias,
        ]);
    }

    /**
     * @Route("/subproceso_update/", name="subproceso_update", methods={"POST"})
     */
    public function updateSubproceso(
        Request $request,
        SubprocesoRepository $subprocesoRepository,
        ManagerRegistry $doctrine
    ): Response {
        $params = $request->request->all();
        $nombre = strtoupper(trim($params['nombre']));
        $id = intval($params['id']);
        //buscamos el subproceso
        $subproceso = $subprocesoRepository->find($id);
        if (!is_null($subproceso)) {
            $subproceso->setNombre($nombre);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($subproceso);
            $entityManager->flush();
        }

        $salida = array("1");
        $response = new Response(json_encode($salida));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/{id}", name="app_subproceso_delete", methods={"POST"})
     */
    public function delete(Request $request, Subproceso $subproceso, ManagerRegistry $doctrine): Response
    {
        if ($this->isCsrfTokenValid('delete' . $subproceso->getId(), $request->request->get('_token'))) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($subproceso);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_subproceso_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/delete_subproceso/", name="delete_subproceso", methods={"POST"})
     */
    public function deleteSubproceso(
        Request $request,
        ManagerRegistry $doctrine,
        SubprocesoRepository $subprocesoRepository
    ): Response {
        $params = $request->request->all();
        $id = $params['id'];
        //--- buscamos la categoria ------//
        $subproceso = $subprocesoRepository->find($id);
        if (!is_null($subproceso)) {
            // existe buscamos el directorio y comprobamos que este vacio   
            $directorio = $subproceso->getDirectorio();
            $filesystem = new Filesystem();
            if ($filesystem->exists($directorio)) {
                //--- buscamos el archivo index.php para borrarlo
                $archivo = $directorio . '/index.php';
                $filesystem->remove($archivo);

                $archivo = $directorio . '/busca_procesos.php';
                $filesystem->remove($archivo);

                $archivo = $directorio . '/busca_subprocesos.php';
                $filesystem->remove($archivo);

                $archivo = $directorio . '/conexion.php';
                $filesystem->remove($archivo);

                $files = glob($directorio . "/*");
                if (empty($files)) {
                    //--- eliminamos el directorio ------//
                    $filesystem->remove($directorio);
                    //--- eliminamos la categoria ------//
                    $entityManager = $doctrine->getManager();
                    $entityManager->remove($subproceso);
                    $entityManager->flush();
                    $estado = '1';
                } else {
                    $estado = '-1';
                }
            } else {
                $estado = '0';
            }
        } else {
            $estado = '0';
        }
        $salida = array($estado);
        $response = new Response(json_encode($salida));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
