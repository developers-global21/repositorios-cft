<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;

use App\Entity\UsuarioCategoria;
use App\Repository\UsuarioCategoriaRepository;

use App\Entity\Categoria;
use App\Repository\CategoriaRepository;

use App\Entity\Subcategoria;
use App\Repository\SubcategoriaRepository;

use App\Entity\Subproceso;
use App\Repository\SubprocesoRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use DateTimeInterface;


/**
 * @IsGranted("ROLE_USER")
 * @Route("/dashboard", name="dashboard")
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="/index_user", methods={"GET"})
     */
    public function index(
        UsuarioCategoriaRepository $usuarioCategoriaRepository,
        CategoriaRepository $categoriaRepository
    ): Response {
        $user = $this->getUser();
        //--- buscamos las categorias asignadas ------
        $misCategorias = $usuarioCategoriaRepository->findUsuarioCategoria($user->getId());
        $allCategorias = $categoriaRepository->findAll();
        $serverName = $_SERVER['SERVER_NAME'];
        $serverPort = $_SERVER['SERVER_PORT'];
        switch ($serverPort) {
            case "80":
                $rutaServidor = "http://" . $serverName . "/";
                break;
            case "8000":
                $rutaServidor = "http://" . $serverName . ":" . $serverPort . "/";
                break;
            case "443":
                $rutaServidor = "https://" . $serverName .  "/";
                break;
        }

        $direccionFinal = $rutaServidor;
        $directorio = $this->getParameter('registros');
        $urlFinal = str_replace($directorio, $direccionFinal, $directorio) . "assets/archivos/";
        return $this->render('dashboard/index.html.twig', [
            'misCategorias',
            'allCategorias' =>  $allCategorias,
            'urlFinal' => $urlFinal,
            'directorio' => $directorio,
        ]);
    }

    /**
     * @Route("/get_procesos/", name="get_procesos", methods={"POST"})
     */
    public function getProceso(
        Request $request,
        ManagerRegistry $doctrine,
        SubcategoriaRepository $subcategoriaRepository
    ): Response {
        $params = $request->request->all();
        $id = intval($params['id']);
        //--- buscamos la categoria ------//
        $procesos = $subcategoriaRepository->findAllSubcategoria($id);
        $salida = array($procesos);
        $response = new Response(json_encode($salida));

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/get_subprocesos/", name="get_subprocesos", methods={"POST"})
     */
    public function getSubproceso(
        Request $request,
        ManagerRegistry $doctrine,
        SubcategoriaRepository $subcategoriaRepository,
        SubprocesoRepository $subprocesoRepository
    ): Response {
        $params = $request->request->all();
        $id = intval($params['id']);
        //--- buscamos la categoria ------//
        $subprocesos = $subprocesoRepository->findAllSubprocesosProceso($id);
        $salida = array($subprocesos);
        $response = new Response(json_encode($salida));

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
