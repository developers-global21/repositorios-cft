<?php

namespace App\Controller;

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
 * @Route("/subcategoria")
 */
class SubcategoriaController extends AbstractController
{
    /**
     * @Route("/", name="app_subcategoria_index", methods={"GET"})
     */
    public function index(SubcategoriaRepository $subcategoriaRepository): Response
    {
        $subcategorias = $subcategoriaRepository->findAll();
        $server_name = $_SERVER['SERVER_NAME'];
        $server_port = $_SERVER['SERVER_PORT'];
        $direccion_finala = "http://" . $server_name . ":" . $server_port . "/";
        $directorio = $this->getParameter('registros');
        $url_final = str_replace($directorio, $direccion_finala, $directorio) . "assets/archivos/";
        return $this->render('subcategoria/index.html.twig', [
            'subcategorias' => $subcategorias,
            'url_final' => $url_final,
            'directorio' => $directorio,
        ]);
    }

    /**
     * @Route("/new", name="app_subcategoria_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ManagerRegistry $doctrine): Response
    {
        $subcategorium = new Subcategoria();
        $form = $this->createForm(SubcategoriaType::class, $subcategorium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($subcategorium);
            $entityManager->flush();

            return $this->redirectToRoute('app_subcategoria_index', [], Response::HTTP_SEE_OTHER);
        }

        //--- buscamos todas la categorias -----

        return $this->renderForm('subcategoria/new.html.twig', [
            'subcategorium' => $subcategorium,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new2", name="app_subcategoria_new2", methods={"GET", "POST"})
     */
    public function newProceso(Request $request, ManagerRegistry $doctrine, CategoriaRepository $categoriaRepository): Response
    {
        /* $subcategorium = new Subcategoria();
        $form = $this->createForm(SubcategoriaType::class, $subcategorium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($subcategorium);
            $entityManager->flush();

            return $this->redirectToRoute('app_subcategoria_index', [], Response::HTTP_SEE_OTHER);
        }*/
        $categorias = $categoriaRepository->findAll();
        return $this->renderForm('subcategoria/new.html.twig', [
            /*'subcategorium' => $subcategorium,
            'form' => $form,*/
            'categorias' => $categorias,
        ]);
    }


    /**
     * @Route("/save_proceso", name="save_proceso", methods={"POST"})
     */
    public function saveProceso(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger, CategoriaRepository $categoriaRepository): Response
    {
        $params = $request->request->all();
        $nombre = strtoupper(trim($params['nombre']));
        $categoriaId = intval($params['categoria']);
        //buscamos la categoria
        $categoria = $categoriaRepository->find($categoriaId);
        if (!is_null($categoria)) {
            // determinamos el directgorio de la categpria
            $directorioCategoria = $categoria->getDirectorio();
            // creamos la rura del proceso
            $directorio = $directorioCategoria . '/' . $slugger->slug($nombre) . "-" . uniqid();

            $subcategoria = new Subcategoria();
            $subcategoria->setNombre($nombre);
            $subcategoria->setDirectorio($directorio);
            $subcategoria->setCategoria($categoria);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($subcategoria);
            $entityManager->flush();
            //--- creamos el directorio 
            $filesystem = new Filesystem();
            $filesystem->mkdir($directorio, 0777);
            $filesystem->copy($this->getParameter('registros') . '/index.php', $directorio . '/index.php');
            $filesystem->copy($this->getParameter('registros') . '/busca_procesos.php', $directorio . '/busca_procesos.php');
            $filesystem->copy($this->getParameter('registros') . '/busca_subprocesos.php', $directorio . '/busca_subprocesos.php');
            $filesystem->chmod($directorio . '/index.php', 0777);
            $filesystem->chmod($directorio . '/busca_procesos.php', 0777);
            $filesystem->chmod($directorio . '/busca_subprocesos.php', 0777);
            $estado = '1';
        } else {
            $estado = '0';
        }

        $salida = array($estado);
        $response = new Response(json_encode($salida));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/{id}", name="app_subcategoria_show", methods={"GET"})
     */
    public function show(Subcategoria $subcategorium): Response
    {
        return $this->render('subcategoria/show.html.twig', [
            'subcategorium' => $subcategorium,
        ]);
    }

    /**
     * @Route("/search_proceso", name="search_proceso", methods={"GET","POST"})
     */
    public function searchProceso(Request $request, SubcategoriaRepository $subcategoriaRepository): Response
    {
        $params = $request->request->all();
        $idProceso = $params['id'];
        $url = $this->generateUrl('proceso_show', [
            'id' => $idProceso,
        ]);
        $respuesta = new Response($url);
        return $respuesta;
    }

    /**
     * @Route("/{id}", name="proceso_show", methods={"GET","POST"})
     */
    public function showProceso(Request $request, SubcategoriaRepository $subcategoriaRepository): Response
    {
        //$idCategoria = $request->query->get('id');
        $params = $request->request->all();
        $idProceso = $params['id'];
        $subcategorium = $subcategoriaRepository->find($idProceso);
        return $this->render('subcategoria/show.html.twig', [
            'subcategorium' => $subcategorium,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_subcategoria_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Subcategoria $subcategorium, ManagerRegistry $doctrine, CategoriaRepository $categoriaRepository): Response
    {
        /*$form = $this->createForm(SubcategoriaType::class, $subcategorium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('app_subcategoria_index', [], Response::HTTP_SEE_OTHER);
        }*/
        $categorias = $categoriaRepository->findAll();
        return $this->renderForm('subcategoria/edit.html.twig', [
            'subcategorium' => $subcategorium,
            'categorias' => $categorias,
        ]);
    }

    /**
     * @Route("/subcategoria_update/", name="subcategoria_update", methods={"POST"})
     */
    public function updateCategoria(
        Request $request,
        SubcategoriaRepository $subcategoriaRepository,
        CategoriaRepository $categoriaRepository,
        ManagerRegistry $doctrine
    ): Response {
        $params = $request->request->all();
        $nombre = strtoupper(trim($params['nombre']));
        $categoriaId = intval($params['categoria']);
        $id = $params['id'];
        //buscamos la categoria
        $categoria = $categoriaRepository->find($categoriaId);
        //--- buscamos la subcategoria ------//
        $subcategoria = $subcategoriaRepository->find($id);
        if (!is_null($subcategoria)) {
            $subcategoria->setNombre($nombre);
            $subcategoria->setCategoria($categoria);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($subcategoria);
            $entityManager->flush();
        }

        $salida = array("1");
        $response = new Response(json_encode($salida));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * @Route("/{id}", name="app_subcategoria_delete", methods={"POST"})
     */
    public function delete(Request $request, Subcategoria $subcategorium, ManagerRegistry $doctrine): Response
    {
        if ($this->isCsrfTokenValid('delete' . $subcategorium->getId(), $request->request->get('_token'))) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($subcategorium);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_subcategoria_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/delete_subcategoria/", name="delete_subcategoria", methods={"POST"})
     */
    public function deleteSubcategoria(
        Request $request,
        ManagerRegistry $doctrine,
        SubcategoriaRepository $subcategoriaRepository
    ): Response {
        $params = $request->request->all();
        $id = $params['id'];
        //--- buscamos la categoria ------//
        $subcategoria = $subcategoriaRepository->find($id);
        if (!is_null($subcategoria)) {
            // existe buscamos el directorio y comprobamos que este vacio   
            $directorio = $subcategoria->getDirectorio();
            $filesystem = new Filesystem();
            if ($filesystem->exists($directorio)) {
                //--- buscamos el archivo index.php para borrarlo
                $archivo = $directorio . '/index.php';
                $filesystem->remove($archivo);
                $archivo = $directorio . '/busca_procesos.php';
                $filesystem->remove($archivo);
                $archivo = $directorio . '/busca_subprocesos.php';
                $filesystem->remove($archivo);

                $files = glob($directorio . "/*");
                if (empty($files)) {
                    //--- eliminamos el directorio ------//
                    $filesystem->remove($directorio);
                    //--- eliminamos la categoria ------//
                    $entityManager = $doctrine->getManager();
                    $entityManager->remove($subcategoria);
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
