<?php

namespace App\Controller;

use App\Entity\EnsayosRealizados;
use App\Entity\Soluciones;
use App\Entity\Reactivos;
use App\Form\EnsayosRealizadosType;
use App\Repository\EnsayosRealizadosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/ensayos/realizados")
 */
class EnsayosRealizadosController extends AbstractController
{
    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @Route("/", name="ensayos_realizados_index", methods="GET")
     */
    public function index(EnsayosRealizadosRepository $ensayosRealizadosRepository, Request $request): Response
    {
        //chequeando los permisos
        $roles = $this->checkingPermission();

        //knp paginator
        $paginator = $this->paginator;
        $ensayos = $paginator->paginate(
            $ensayosRealizadosRepository->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );

        return $this->render('ensayos_realizados/index.html.twig', [
            'ensayos_realizados' => $ensayos,
            'roles' => $roles,
            'username'=> $_SESSION['fos_user_logged_name']
            ]);
    }

    /**
     * @Route("/new", name="ensayos_realizados_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        //chequeando los permisos
        $roles = $this->checkingPermission();
        //chequeando los permisos

        $arrayValue = $request->request->all();
        $repository = $this->getDoctrine()->getRepository(Soluciones::class);
        $ensayosRealizado = new EnsayosRealizados();
        $form = $this->createForm(EnsayosRealizadosType::class, $ensayosRealizado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($arrayValue as $key=>$value) {
                $key = str_replace('_',' ', $key);

                $pos = (strpos($key,'soluciones'));
                if($pos !== false){
                    $solucion = $repository->find(intval($value));
                    $ensayosRealizado->addSolucione($solucion);
                }
            }
            $fecha = $request->request->get('fecha');
            $ensayosRealizado->setHechoPor($_SESSION['fos_user_logged_name']);
            $ensayosRealizado->setFecha($fecha);

            $em = $this->getDoctrine()->getManager();
            $em->persist($ensayosRealizado);
            $em->flush();

            return $this->redirectToRoute('ensayos_realizados_index');
        }

        $all_soluciones = $repository->findAll();

        return $this->render('ensayos_realizados/new.html.twig', [
            'ensayos_realizado' => $ensayosRealizado,
            'form' => $form->createView(),
            'roles' => $roles,
            'username'=> $_SESSION['fos_user_logged_name'],
            'all_soluciones' => $all_soluciones,
            'soluciones' => null
        ]);
    }

    /**
     * @Route("/{id}", name="ensayos_realizados_show", methods="GET")
     */
    public function show(EnsayosRealizados $ensayosRealizado): Response
    {
        //chequeando los permisos
        $roles = $this->checkingPermission();
        //chequeando los permisos

        return $this->render('ensayos_realizados/show.html.twig', ['ensayos_realizado' => $ensayosRealizado, 'roles'=>$roles,'username'=> $_SESSION['fos_user_logged_name']]);
    }

    /**
     * @Route("/{id}/edit", name="ensayos_realizados_edit", methods="GET|POST")
     */
    public function edit(Request $request, EnsayosRealizados $ensayosRealizado): Response
    {
        //chequeando los permisos
        $roles = $this->checkingPermission();
        //chequeando los permisos

        $form = $this->createForm(EnsayosRealizadosType::class, $ensayosRealizado);
        $form->handleRequest($request);
        $repository = $this->getDoctrine()->getRepository(Soluciones::class);
        $all_soluciones = $repository->findAll();

        $arrayInputs = $request->request->all();

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($arrayInputs as $key => $value){
                $key = str_replace('_',' ', $key);

                $pos = (strpos($key,'soluciones'));
                if($pos !== false){
                    $solucion = $repository->find(intval($value));
                    $ensayosRealizado->addSolucione($solucion);
                }
            }
            $fecha = $request->request->get('fecha');
            $ensayosRealizado->setHechoPor($_SESSION['fos_user_logged_name']);
            $ensayosRealizado->setFecha($fecha);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ensayos_realizados_index', ['id' => $ensayosRealizado->getId()]);
        }

        //para mostrar los reactivos asociados a la solucion...
        $soluciones = $this->solutionRelatedToTest($ensayosRealizado);

        return $this->render('ensayos_realizados/edit.html.twig', [
            'ensayos_realizado' => $ensayosRealizado,
            'form' => $form->createView(),
            'roles'=>$roles,
            'username'=> $_SESSION['fos_user_logged_name'],
            'all_soluciones' => $all_soluciones,
            'soluciones' => $soluciones //soluciones asociadas al ensayo
        ]);
    }

    /**
     * @Route("/{id}", name="ensayos_realizados_delete", methods="DELETE")
     */
    public function delete(Request $request, EnsayosRealizados $ensayosRealizado): Response
    {
        //chequeando los permisos
        $roles = $this->checkingPermission();
        //chequeando los permisos

        if ($this->isCsrfTokenValid('delete'.$ensayosRealizado->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ensayosRealizado);
            $em->flush();
        }

        return $this->redirectToRoute('ensayos_realizados_index');
    }

    /**
     * chequear los permisos
     */
    private function checkingPermission()
    {
        $roles = null;

        //checking the permissions(start)
        if(!isset($_SESSION['fos_user_roles']))
            return $this->redirectToRoute('main');

        if($_SESSION['fos_user_roles']){
            $roles = $_SESSION['fos_user_roles'];
            $result = '';

            foreach ($roles as $value)
                if($value == 'ROLE_ANALISTA' || $value == 'ROLE_ADMIN' || $value == 'ROLE_JEFE_EQUIPO' || $value == 'ROLE_ANALISTA_PRINCIPAL')
                    $result = $value;

            if($result == '')
                return $this->redirectToRoute('main');
        }//checking the permissions(end)

        return $roles;
    }

    /**
     * este metodo retorna las soluciones asociadas al ensayo-realizado de parametro
     */
    private function solutionRelatedToTest(EnsayosRealizados $ensayosRealizado)
    {
        $id = $ensayosRealizado->getId();
        $em = $this->getDoctrine()->getManager();

        $sql2 = "SELECT Soluciones.id as sid, Soluciones.nombre as snombre
                 FROM Soluciones INNER JOIN ensayos_realizados_soluciones ON Soluciones.id = ensayos_realizados_soluciones.soluciones_id AND ensayos_realizados_soluciones.ensayos_realizados_id = $id";
        $statement = $em->getConnection()->prepare($sql2);
        $statement->execute();

        $soluciones = $statement->fetchAll();

        return $soluciones;
    }
}
