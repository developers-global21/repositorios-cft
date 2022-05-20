<?php

namespace App\Controller;

use App\Entity\Registro;
use App\Form\RegistroType;
use App\Repository\RegistroRepository;

use App\Entity\UsuarioCategoria;
use App\Repository\UsuarioCategoriaRepository;

use App\Entity\Categoria;
use App\Repository\CategoriaRepository;

use App\Entity\Subbcategoria;
use App\Repository\SubcategoriaRepository;

use App\Entity\Subproceso;
use App\Repository\SubprocesoRepository;

use App\Entity\Periodo;
use App\Repository\PeriodoRepository;

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
use Knp\Component\Pager\PaginatorInterface;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/registro")
 */
class RegistroController extends AbstractController
{
    /**
     * @Route("/", name="app_registro_index", methods={"GET","POST"})
     */
    public function index(
        Request $request,
        CategoriaRepository $categoriaRepository,
        SubcategoriaRepository $subcategoriaRepository,
        SubprocesoRepository $subprocesoRepository,
        UsuarioCategoriaRepository $usuarioCategoriaRepository,
        PeriodoRepository $periodoRepository,
        RegistroRepository $registroRepository,
        PaginatorInterface $paginator
    ): Response {
        $user = $this->getUser();
        // buscamos la categoria asignada al usuario
        $categoriaUser = $usuarioCategoriaRepository->findUsuarioCategoria($user->getId());
        $idCategoria = $categoriaUser[0]['categoria_id'];

        if (!is_null($request->query->get('categoriaId'))) {
            $categoriaId = $request->query->get('categoriaId');
        } else {
            $categoriaId = '-99';
        }

        if (!is_null($request->query->get('procesoId'))) {
            $procesoId = $request->query->get('procesoId');
        } else {
            $procesoId = '-99';
        }

        if (!is_null($request->query->get('subprocesoId'))) {
            $subProcesoId = $request->query->get('subprocesoId');
        } else {
            $subProcesoId = '-99';
        }

        if (!is_null($request->query->get('periodoId'))) {
            $periodoId = $request->query->get('periodoId');
        } else {
            $periodoId = '-99';
        }

        //--- buscamos todas las categorias --------
        $categorias = $categoriaRepository->findAll();

        //---buscamos todos los periodos ------
        $periodos = $periodoRepository->findAll();

        //---- buscamos las subcategorias ------        
        if ($categoriaId != '-99') {
            $subCategorias = $subcategoriaRepository->findBy(['categoria' => intval($categoriaId)]);
            if ($procesoId != '-99') {
                $subProcesos = $subprocesoRepository->findBy(['categoria' => $categoriaId, 'subcategoria' => $procesoId]);
                if ($subProcesoId != '-99') {
                    if ($periodoId != '-99') {
                        $misRegistros = $registroRepository->findBy([
                            'categoria' => $categoriaId,
                            'subcategoria' => $procesoId,
                            'subproceso' => $subProcesoId,
                            'periodo' => $periodoId
                        ]);
                    } else {
                        $misRegistros = $registroRepository->findBy([
                            'categoria' => $categoriaId,
                            'subcategoria' => $procesoId,
                            'subproceso' => $subProcesoId
                        ]);
                    }
                } else {
                    if ($periodoId != '-99') {
                        $misRegistros = $registroRepository->findBy([
                            'categoria' => $categoriaId,
                            'subcategoria' => $procesoId,
                            'periodo' => $periodoId
                        ]);
                    } else {
                        $misRegistros = $registroRepository->findBy([
                            'categoria' => $categoriaId,
                            'subcategoria' => $procesoId
                        ]);
                    }
                }
            } else {
                if ($periodoId != '-99') {
                    $misRegistros = $registroRepository->findBy([
                        'categoria' => $categoriaId,
                        'periodo' => $periodoId
                    ]);
                } else {
                    $misRegistros = $registroRepository->findBy([
                        'categoria' => $categoriaId
                    ]);
                }
                $subProcesos = NULL;
            }
        } else {
            $subCategorias = NULL;
            $subProcesos = NULL;
            if ($periodoId != '-99') {
                $misRegistros = $registroRepository->findBy([
                    'periodo' => $periodoId
                ]);
            } else {
                $misRegistros = $registroRepository->findAll();
            }
        }

        // buscamos los registros correspondientes a esta categoria

        //$misRegistros = $registroRepository->findAll();
        $canReg = $request->query->getInt('can_reg', 20);

        // Paginar los resultados de la consulta
        $registros = $paginator->paginate(
            // Consulta Doctrine, no resultados
            $misRegistros,
            // Definir el parámetro de la página
            $request->query->getInt('page', 1),
            // Items per page
            $canReg
        );

        return $this->render('registro/index.html.twig', [
            'registros' => $registros,
            'canReg' => $canReg,
            'categorias' => $categorias,
            'subCategorias' => $subCategorias,
            'subProcesos' => $subProcesos,
            'periodos' => $periodos,
            'categoriaId' => $categoriaId,
            'procesoId' => $procesoId,
            'subProcesoId' => $subProcesoId,
            'periodoId' => $periodoId,
        ]);
    }

    /**
     * @Route("/new", name="app_registro_new", methods={"GET", "POST"})
     */
    public function new(
        Request $request,
        UsuarioCategoriaRepository $usuarioCategoriaRepository,
        PeriodoRepository $periodoRepository,
        CategoriaRepository $categoriaRepository,
        SubcategoriaRepository $subcategoriaRepository
    ): Response {
        $user = $this->getUser();

        // buscamos la categoria asignada al usuario
        $categoriaUser = $usuarioCategoriaRepository->findUsuarioCategoria($user->getId());
        $idCategoria = $categoriaUser[0]['categoria_id'];
        $misCategorias = $categoriaRepository->find(intval($idCategoria));

        $allCategorias = $categoriaRepository->findAll();

        //--- buscamos los Procesos perteneceintes a esta categoria (subcategoria)
        $misProcesos = $subcategoriaRepository->findBy(['categoria' => $idCategoria]);

        //--- buscamos los periodos
        $periodos = $periodoRepository->findAll();
        return $this->renderForm('registro/new2.html.twig', [
            'misCategorias' => $misCategorias,
            'misProcesos' => $misProcesos,
            'misPeriodos' => $periodos,
            'allCategorias' => $allCategorias,
        ]);
    }

    /**
     * @Route("/{id}", name="app_registro_show", methods={"GET"})
     */
    public function show(Registro $registro): Response
    {
        return $this->render('registro/show.html.twig', [
            'registro' => $registro,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_registro_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Registro $registro, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(RegistroType::class, $registro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('app_registro_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('registro/edit.html.twig', [
            'registro' => $registro,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_registro_delete", methods={"POST"})
     */
    public function delete(Request $request, Registro $registro, ManagerRegistry $doctrine): Response
    {
        if ($this->isCsrfTokenValid('delete' . $registro->getId(), $request->request->get('_token'))) {
            $entityManager = $doctrine->getManager();
            $entityManager->remove($registro);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_registro_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Esta funcion permite grabar archivo  subido por un responsable de categoria
     * @ params $file0 Referencia a un archivo subido desde el formulario de autoevaluacion
     * @ params int $maxFileSize Maximo tamaño del archivo  
     * @ params $filesPermit cadena contentiva de las extensiones permitidas como archivos válidos
     * @ params SluggerInterface $slugger una referencia a slugger que permite sanitizar los nombres de los archivos
     * @ params string $comment comentatrio del autoevaluador
     */
    private function newFileRegistro(
        $file0,
        int $maxFileSize,
        string $filesPermit,
        SluggerInterface $slugger,
        CategoriaRepository $categoriaRepository,
        SubcategoriaRepository $subcategoriaRepository,
        SubprocesoRepository $subprocesoRepository,
        PeriodoRepository $periodoRepository,
        ManagerRegistry $doctrine,
        int $categoriaId,
        int $subcategoriaId,
        int $subprocesoId,
        int $periodoId
    ) {
        $user = $this->getUser();
        if (!empty($file0) && $file0 != 'vacio') {
            //-- determinamos la categoria 
            $categoria = $categoriaRepository->find($categoriaId);

            //-- determinamos la subcategoria
            $subcategoria = $subcategoriaRepository->find($subcategoriaId);

            //-- determinamos el subproceso
            $subproceso = $subprocesoRepository->find($subprocesoId);

            //-- determinamos el periodo
            $periodo = $periodoRepository->find($periodoId);

            //--- determinamos el directorio de destino
            if ($subproceso) {
                $directorio_final = $subproceso->getDirectorio() . "/";
            } else {
                $directorio_final = $subcategoria->getDirectorio() . "/";
            }
            if ($subproceso) {
                $directorio_final2 = (str_replace($this->getParameter('registros'), '/assets/archivos/', $subproceso->getDirectorio())) . "/";
            } else {
                $directorio_final2 = (str_replace($this->getParameter('registros'), '/assets/archivos/', $subcategoria->getDirectorio())) . "/";
            }

            $originalFilename = pathinfo($file0->getClientOriginalName(), PATHINFO_FILENAME);
            $fileExtension0 = strtoupper($file0->guessExtension());
            //--- se verifica la extensión el archivo gráfico
            if (strpos($filesPermit, $fileExtension0) === false) {
                $newFilename0 = 'error ext';
            } else {
                //if (($fileExtension0 == 'JPEG') || ($fileExtension0 == 'JPG') || ($fileExtension0 == 'PNG') || ($fileExtension0 == 'GIF')) {
                // this is needed to safely include the file name as part of the URL
                // 20971520 valor definido en el .env
                $fileSize0 =   $file0->getSize();

                if ($fileSize0 <= $maxFileSize) {
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename0 = $safeFilename . '-' . uniqid() . '.' . $file0->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $file0->move($directorio_final, $newFilename0);
                    } catch (FileException $e) {
                        $newFilename0 = $e;
                    }
                } else {
                    $newFilename0 = 'error size';
                }
            }
        }
        //--- no hay errores en el nombre del archivo Guardamos en la BD----//
        $entityManager = $doctrine->getManager();
        if (($newFilename0 != 'error size') && ($newFilename0 != 'error ext') && ($newFilename0 != 'vacio')) {
            $fileRecord = new Registro();
            $fileRecord->setNombre($originalFilename);
            $fileRecord->setCategoria($categoria);
            $fileRecord->setSubcategoria($subcategoria);
            if ($subproceso) {
                $fileRecord->setSubproceso($subproceso);
            } else {
                $fileRecord->setSubproceso(0);
            }
            $fileRecord->setPeriodo($periodo);
            $fileRecord->setUrl($directorio_final2 . $newFilename0);


            //--- hacemos persistente el cambio
            $entityManager = $doctrine->getManager();
            $entityManager->persist($fileRecord);
            $entityManager->flush();
        }
        return ($newFilename0);
    }

    /**
     * @Route("/save_registro/", name="save_registro", methods={"POST"})
     */
    public function saveRegistro(
        Request $request,
        ManagerRegistry $doctrine,
        SluggerInterface $slugger,
        CategoriaRepository $categoriaRepository,
        SubcategoriaRepository $subcategoriaRepository,
        SubprocesoRepository $subprocesoRepository,
        PeriodoRepository $periodoRepository,
        RegistroRepository $registroRepository
    ): Response {
        $salida = array("1");
        $params = $request->request->all();
        $user = $this->getUser();
        $idCategoria = $params['categoria'];
        $idSubcategoria = $params['proceso'];
        $idSubproceso = $params['subproceso'];
        $idPeriodo = $params['periodo'];

        $maxFileSize = 5242880; // 5 * 1024 * 1024 bytes
        $filesPermit = 'XLS XLSX ODP PPT PPTX DOC DOCX ODT ODF PDF ODS ZIP RAR PNG JPEG JPG GIF';

        //////////////// inclusion de archivos ////////////////////////////////////////
        //--- archivo 0 ----//
        $newArchivoname0 = 'vacio';
        $newArchivoname1 = 'vacio';
        $newArchivoname2 = 'vacio';
        $newArchivoname3 = 'vacio';
        $newArchivoname4 = 'vacio';
        $newArchivoname5 = 'vacio';
        $newArchivoname6 = 'vacio';
        $newArchivoname7 = 'vacio';
        $newArchivoname8 = 'vacio';
        $newArchivoname9 = 'vacio';

        $archivo0 = $request->files->get('archivo0');
        $archivo1 = $request->files->get('archivo1');
        $archivo2 = $request->files->get('archivo2');
        $archivo3 = $request->files->get('archivo3');
        $archivo4 = $request->files->get('archivo4');
        $archivo5 = $request->files->get('archivo5');
        $archivo6 = $request->files->get('archivo6');
        $archivo7 = $request->files->get('archivo7');
        $archivo8 = $request->files->get('archivo8');
        $archivo9 = $request->files->get('archivo9');



        if (!empty($archivo0) && ($archivo0 != 'vacio')) {
            $newArchivoname0 = $this->newFileRegistro(
                $archivo0,
                $maxFileSize,
                $filesPermit,
                $slugger,
                $categoriaRepository,
                $subcategoriaRepository,
                $subprocesoRepository,
                $periodoRepository,
                $doctrine,
                $idCategoria,
                $idSubcategoria,
                $idSubproceso,
                $idPeriodo
            );
        }

        if (!empty($archivo1) && ($archivo1 != 'vacio')) {
            $newArchivoname1 = $this->newFileRegistro(
                $archivo1,
                $maxFileSize,
                $filesPermit,
                $slugger,
                $categoriaRepository,
                $subcategoriaRepository,
                $subprocesoRepository,
                $periodoRepository,
                $doctrine,
                $idCategoria,
                $idSubcategoria,
                $idSubproceso,
                $idPeriodo
            );
        }

        if (!empty($archivo2) && ($archivo2 != 'vacio')) {
            $newArchivoname2 = $this->newFileRegistro(
                $archivo2,
                $maxFileSize,
                $filesPermit,
                $slugger,
                $categoriaRepository,
                $subcategoriaRepository,
                $subprocesoRepository,
                $periodoRepository,
                $doctrine,
                $idCategoria,
                $idSubcategoria,
                $idSubproceso,
                $idPeriodo
            );
        }

        if (!empty($archivo3) && ($archivo3 != 'vacio')) {
            $newArchivoname3 = $this->newFileRegistro(
                $archivo3,
                $maxFileSize,
                $filesPermit,
                $slugger,
                $categoriaRepository,
                $subcategoriaRepository,
                $subprocesoRepository,
                $periodoRepository,
                $doctrine,
                $idCategoria,
                $idSubcategoria,
                $idSubproceso,
                $idPeriodo
            );
        }

        if (!empty($archivo4) && ($archivo4 != 'vacio')) {
            $newArchivoname4 = $this->newFileRegistro(
                $archivo4,
                $maxFileSize,
                $filesPermit,
                $slugger,
                $categoriaRepository,
                $subcategoriaRepository,
                $subprocesoRepository,
                $periodoRepository,
                $doctrine,
                $idCategoria,
                $idSubcategoria,
                $idSubproceso,
                $idPeriodo
            );
        }

        if (!empty($archivo5) && ($archivo5 != 'vacio')) {
            $newArchivoname5 = $this->newFileRegistro(
                $archivo5,
                $maxFileSize,
                $filesPermit,
                $slugger,
                $categoriaRepository,
                $subcategoriaRepository,
                $subprocesoRepository,
                $periodoRepository,
                $doctrine,
                $idCategoria,
                $idSubcategoria,
                $idSubproceso,
                $idPeriodo
            );
        }

        if (!empty($archivo6) && ($archivo6 != 'vacio')) {
            $newArchivoname6 = $this->newFileRegistro(
                $archivo6,
                $maxFileSize,
                $filesPermit,
                $slugger,
                $categoriaRepository,
                $subcategoriaRepository,
                $subprocesoRepository,
                $periodoRepository,
                $doctrine,
                $idCategoria,
                $idSubcategoria,
                $idSubproceso,
                $idPeriodo
            );
        }

        if (!empty($archivo7) && ($archivo7 != 'vacio')) {
            $newArchivoname7 = $this->newFileRegistro(
                $archivo7,
                $maxFileSize,
                $filesPermit,
                $slugger,
                $categoriaRepository,
                $subcategoriaRepository,
                $subprocesoRepository,
                $periodoRepository,
                $doctrine,
                $idCategoria,
                $idSubcategoria,
                $idSubproceso,
                $idPeriodo
            );
        }

        if (!empty($archivo8) && ($archivo8 != 'vacio')) {
            $newArchivoname8 = $this->newFileRegistro(
                $archivo8,
                $maxFileSize,
                $filesPermit,
                $slugger,
                $categoriaRepository,
                $subcategoriaRepository,
                $subprocesoRepository,
                $periodoRepository,
                $doctrine,
                $idCategoria,
                $idSubcategoria,
                $idSubproceso,
                $idPeriodo
            );
        }

        if (!empty($archivo9) && ($archivo9 != 'vacio')) {
            $newArchivoname9 = $this->newFileRegistro(
                $archivo9,
                $maxFileSize,
                $filesPermit,
                $slugger,
                $categoriaRepository,
                $subcategoriaRepository,
                $subprocesoRepository,
                $periodoRepository,
                $doctrine,
                $idCategoria,
                $idSubcategoria,
                $idSubproceso,
                $idPeriodo
            );
        }


        $salida = array("1", $newArchivoname0, $newArchivoname1, $newArchivoname2, $newArchivoname3, $newArchivoname4, $newArchivoname5, $newArchivoname6, $newArchivoname7, $newArchivoname8, $newArchivoname9);
        $response = new Response(json_encode($salida));

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/delete_registro/", name="delete_registro", methods={"POST"})
     */
    public function deleteRegistro(Request $request, ManagerRegistry $doctrine, RegistroRepository $registroRepository): Response
    {
        $params = $request->request->all();
        $id = $params['id'];
        //--- buscamos la categoria ------//
        $registro = $registroRepository->find($id);
        if (!is_null($registro)) {
            // existe buscamos el directorio y comprobamos que este vacio  
            ///home/egonzalez/composer/symphony/repositorio/public/assets/archivos// 
            $url = str_replace('/assets/archivos/', '', $this->getParameter('registros')) . $registro->getUrl();
            $filesystem = new Filesystem();
            if ($filesystem->exists($url)) {
                //--- eliminamos el directorio ------//
                $filesystem->remove($url);
                //--- eliminamos la categoria ------//
                $entityManager = $doctrine->getManager();
                $entityManager->remove($registro);
                $entityManager->flush();
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
}
