<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Form\CategoriaType;
use App\Repository\CategoriaRepository;

use App\Entity\Subcategoria;
use App\Form\SubcategoriaType;
use App\Repository\SubcategoriaRepository;

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
 * @Route("/categoria")
 */
class CategoriaController extends AbstractController
{
    /**
     * @Route("/", name="app_categoria_index", methods={"GET"})
     */
    public function index(CategoriaRepository $categoriaRepository): Response
    {
        $categorias = $categoriaRepository->findAll();
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
        return $this->render('categoria/index.html.twig', [
            'categorias' => $categorias,
            'url_final' => $url_final,
            'directorio' => $directorio,
        ]);
    }

    /**
     * @Route("/new", name="app_categoria_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $categorium = new Categoria();
        $form = $this->createForm(CategoriaType::class, $categorium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($categorium);
            $entityManager->flush();

            return $this->redirectToRoute('app_categoria_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('categoria/new.html.twig', [
            'categorium' => $categorium,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new2", name="app_categoria_new2", methods={"GET", "POST"})
     */
    public function newCategoria(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        return $this->renderForm('categoria/new_categoria.html.twig', []);
    }

    /**
     * @Route("/save_categoria", name="app_save_categoria", methods={"POST"})
     */
    public function saveCategoria(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $params = $request->request->all();
        $nombre = strtoupper(trim($params['nombre']));
        $directorio = $this->getParameter('registros') . $slugger->slug(trim($params['nombre'])) . "-" . uniqid();
        $categoria = new Categoria();
        $categoria->setNombre($nombre);
        $categoria->setDirectorio($directorio);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($categoria);
        $entityManager->flush();
        //--- creamos el directorio 
        $filesystem = new Filesystem();
        $filesystem->mkdir($directorio, 0777);
        $filesystem->copy($this->getParameter('registros') . '/index.php', $directorio . '/index.php');
        $filesystem->copy($this->getParameter('registros') . '/busca_procesos.php', $directorio . '/busca_procesos.php');
        $filesystem->copy($this->getParameter('registros') . '/busca_subprocesos.php', $directorio . '/busca_subprocesos.php');
        $filesystem->copy($this->getParameter('registros') . '/conexion.php', $directorio . '/conexion.php');
        $filesystem->chmod($directorio . '/index.php', 0777);
        $filesystem->chmod($directorio . '/busca_procesos.php', 0777);
        $filesystem->chmod($directorio . '/busca_subprocesos.php', 0777);
        $filesystem->chmod($directorio . '/conexion.php', 0777);
        $salida = array("1");
        $response = new Response(json_encode($salida));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/{id}", name="app_categoria_show", methods={"GET"})
     */
    public function show(Categoria $categorium): Response
    {
        return $this->render('categoria/show.html.twig', [
            'categorium' => $categorium,
        ]);
    }

    /**
     * @Route("/search_categoria", name="search_categoria", methods={"GET","POST"})
     */
    public function searchCategoria(Request $request, CategoriaRepository $categoriaRepository): Response
    {
        $params = $request->request->all();
        $idCategoria = $params['id'];
        $url = $this->generateUrl('categoria_show', [
            'id' => $idCategoria,
        ]);
        $respuesta = new Response($url);
        return $respuesta;
    }


    /**
     * @Route("/{id}", name="categoria_show", methods={"GET","POST"})
     */
    public function showCategoria(Request $request, CategoriaRepository $categoriaRepository): Response
    {
        //$idCategoria = $request->query->get('id');
        $params = $request->request->all();
        $idCategoria = $params['id'];
        $categorium = $categoriaRepository->find($idCategoria);
        return $this->render('categoria/show.html.twig', [
            'categorium' => $categorium,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_categoria_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Categoria $categorium, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(CategoriaType::class, $categorium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('app_categoria_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categoria/edit.html.twig', [
            'categorium' => $categorium,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="categoria_edit", methods={"GET"})
     */
    public function editCategoria(Request $request, CategoriaRepository $categoriaRepository): Response
    {
        $categorium = $categoriaRepository->find($request->query->get('id'));
        return $this->render('categoria/edit.html.twig', [
            'categorium' => $categorium,
        ]);
    }


    /**
     * @Route("/categoria_update/", name="categoria_update", methods={"POST"})
     */
    public function updateCategoria(Request $request, CategoriaRepository $categoriaRepository, ManagerRegistry $doctrine): Response
    {
        $params = $request->request->all();
        $nombre = strtoupper(trim($params['nombre']));
        $id = $params['id'];
        //--- buscamos la categoria ------//
        $categoria = $categoriaRepository->find($id);
        if (!is_null($categoria)) {
            $categoria->setNombre($nombre);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($categoria);
            $entityManager->flush();
        }

        $salida = array("1");
        $response = new Response(json_encode($salida));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * @Route("/{id}", name="app_categoria_delete", methods={"POST"})
     */
    public function delete(Request $request, Categoria $categorium, ManagerRegistry $doctrine): Response
    {
        if ($this->isCsrfTokenValid('delete' . $categorium->getId(), $request->request->get('_token'))) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($categorium);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categoria_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/delete_categoria/", name="delete_categoria", methods={"POST"})
     */
    public function deleteCategoria(Request $request, ManagerRegistry $doctrine, CategoriaRepository $categoriaRepository): Response
    {
        $params = $request->request->all();
        $id = $params['id'];
        //--- buscamos la categoria ------//
        $categoria = $categoriaRepository->find($id);
        if (!is_null($categoria)) {
            // existe buscamos el directorio y comprobamos que este vacio   
            $directorio = $categoria->getDirectorio();
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
                    $entityManager->remove($categoria);
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

    /**
     * @Route("/get_subcategoria/", name="get_subcategoria", methods={"POST"})
     */
    public function getSubcategoria(
        Request $request,
        CategoriaRepository $categoriaRepository,
        SubcategoriaRepository $subcategoriaRepository,
        ManagerRegistry $doctrine
    ): Response {
        $params = $request->request->all();
        $id = intval($params['id']);
        //--- buscamos la categoria ------//
        $categoria = $categoriaRepository->find($id);
        if (!is_null($categoria)) {
            $subcategorias = $subcategoriaRepository->findAllSubcategoria($id);
        } else {
            $subcategorias = null;
        }
        $response = new Response(json_encode($subcategorias));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
