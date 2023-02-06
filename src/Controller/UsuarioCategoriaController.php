<?php

namespace App\Controller;

use App\Entity\UsuarioCategoria;
use App\Form\UsuarioCategoriaType;
use App\Repository\UsuarioCategoriaRepository;

use App\Entity\User;
use App\Repository\UserRepository;

use App\Entity\Categoria;
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

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/usuario_categoria")
 */
class UsuarioCategoriaController extends AbstractController
{
    /**
     * @Route("/", name="app_usuario_categoria_index", methods={"GET"})
     */
    public function index(UsuarioCategoriaRepository $usuarioCategoriaRepository): Response
    {
        $usuariosCategorias = $usuarioCategoriaRepository->findAllUsuarioCategoria();

        return $this->render('usuario_categoria/index.html.twig', [
            'usuariosCategorias' => $usuariosCategorias,
        ]);
    }

    /**
     * @Route("/new", name="app_usuario_categoria_new", methods={"GET", "POST"})
     */
    public function new(UserRepository $userRepository, CategoriaRepository $categoriaRepository): Response
    {
        $usuarios = $userRepository->findAll();
        $categorias = $categoriaRepository->findAll();

        return $this->renderForm('usuario_categoria/new.html.twig', [
            'usuarios' => $usuarios,
            'categorias' => $categorias,
        ]);
    }

    /**
     * @Route("/save_usuario_categoria", name="save_usuario_categoria", methods={"GET", "POST"})
     */
    public function saveusuarioCategoria(Request $request, UserRepository $userRepository, CategoriaRepository $categoriaRepository, ManagerRegistry $doctrine): Response
    {
        $params = $request->request->all();
        //-- buscamos el usuario --//
        $userId = intval($params['usuario']);
        $categoriaId = intval($params['categoria']);

        $usuario = $userRepository->find($userId);
        $categoria = $categoriaRepository->find($categoriaId);
        $usuariuoCategoria = new UsuarioCategoria();
        $usuariuoCategoria->setUser($usuario);
        $usuariuoCategoria->setCategoria($categoria);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($usuariuoCategoria);
        $entityManager->flush();

        $salida = array("1");
        $response = new Response(json_encode($salida));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/{id}", name="app_usuario_categoria_show", methods={"GET"})
     */
    public function show(UsuarioCategoria $usuarioCategorium): Response
    {
        return $this->render('usuario_categoria/show.html.twig', [
            'usuario_categorium' => $usuarioCategorium,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_usuario_categoria_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, UsuarioCategoria $usuarioCategorium, UserRepository $userRepository,  CategoriaRepository $categoriaRepository): Response
    {
        $categorias = $categoriaRepository->findAll();
        $userId = $usuarioCategorium->getUser()->getId();
        $usuario = $userRepository->find($userId);
        return $this->renderForm('usuario_categoria/edit.html.twig', [
            'usuarioCategoria' => $usuarioCategorium,
            'categorias' => $categorias,
            'usuario' => $usuario,
        ]);
    }


    /**
     * @Route("/update_usuario_categoria/", name="update_usuario_categoria", methods={"GET", "POST"})
     */
    public function updateUsuarioCategoria(
        Request $request,
        UserRepository $userRepository,
        CategoriaRepository $categoriaRepository,
        UsuarioCategoriaRepository $usuarioCategoriaRepository,
        ManagerRegistry $doctrine
    ): Response {
        $params = $request->request->all();
        //-- buscamos el usuario --//
        $userId = intval($params['usuario']);
        $categoriaId = intval($params['categoria']);
        $id = intval($params['id']);
        $usuario = $userRepository->find($userId);
        $categoria = $categoriaRepository->find($categoriaId);
        $usuarioCategoria = $usuarioCategoriaRepository->find($id);
        $usuarioCategoria->setCategoria($categoria);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($usuarioCategoria);
        $entityManager->flush();

        $salida = array("1");
        $response = new Response(json_encode($salida));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/{id}", name="app_usuario_categoria_delete", methods={"POST"})
     */
    public function delete(Request $request, UsuarioCategoria $usuarioCategorium, ManagerRegistry $doctrine): Response
    {
        if ($this->isCsrfTokenValid('delete' . $usuarioCategorium->getId(), $request->request->get('_token'))) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($usuarioCategorium);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_usuario_categoria_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/delete_usuario_categoria/", name="delete_usuario_categoria", methods={"GET", "POST"})
     */
    public function deleteUsuarioCategoria(
        Request $request,
        UsuarioCategoriaRepository $usuarioCategoriaRepository,
        ManagerRegistry $doctrine
    ): Response {
        $params = $request->request->all();
        /*
            userId:userIdv,
            categoriaId:categoriaIdv,

        */
        //-- buscamos el usuario --//
        $userCategoriaId = intval($params['categoriaId']);
        $registro = $usuarioCategoriaRepository->find($userCategoriaId);
        if ($registro) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($registro);
            $entityManager->flush();
            $salida = array("1");
        } else {
            $salida = array("-1");
        }

        $response = new Response(json_encode($salida));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
