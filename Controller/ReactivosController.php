<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Entity\Reactivos;
use App\Entity\SolucionesReactivosCantidad;
use App\Form\ReactivosType;
use App\Repository\ReactivosRepository;
use ReactivosCategoria\ReactivosCategorias;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ReactivosCategoria;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/reactivos")
 */
class ReactivosController extends AbstractController
{
    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @Route("/", name="reactivos_index", methods="GET")
     */
    public function index(ReactivosRepository $reactivosRepository, Request $request): Response
    {
        //checking the permissions(start)
        $roles = $this->checkingPermission();
        //checking the permissions(end)


        $em = $this->getDoctrine()->getManager();
        $sql = 'SELECT Reactivos.id, Categoria.nombre
             FROM Reactivos LEFT JOIN reactivos_categoria ON Reactivos.id = reactivos_categoria.reactivos_id
              RIGHT JOIN Categoria ON Categoria.id = reactivos_categoria.categoria_id';
        $statement = $em->getConnection()->prepare($sql);
        $statement->execute();
        $categorias = $statement->fetchAll();

        $repository = $this->getDoctrine()->getRepository(Categoria::class);
        $all_categoria = $repository->findAll();

        //knp paginator
        $paginator = $this->paginator;
        $reactivos = $paginator->paginate(
            $reactivosRepository->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );

        return $this->render('reactivos/index.html.twig', [
            'reactivos' => $reactivos,
            'roles' => $roles,
            'categorias' => $categorias,
            'username'=> $_SESSION['fos_user_logged_name'],
            'all_categoria' => $all_categoria]);
    }

    /**
     * @Route("/new", name="reactivos_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        //checking the permissions
        $roles = $this->checkingPermission();
        //checking the permissions


        $reactivo = new Reactivos();
        $form = $this->createForm(ReactivosType::class, $reactivo);
        $form->handleRequest($request);

        $fisic_state = '';
        if(isset($request->request->all()['estado_fisico']))
            $fisic_state = $request->request->all()['estado_fisico'];//new


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $reactivo->setEstadoFisico($fisic_state);
            $em->persist($reactivo);
            $em->flush();

            return $this->redirectToRoute('reactivos_index');
        }

        return $this->render('reactivos/new.html.twig', [
            'reactivo' => $reactivo,
            'form' => $form->createView(),
            'roles' => $roles,
            'username'=>$_SESSION['fos_user_logged_name']
        ]);
    }

    /**
     * @Route("/{id}", name="reactivos_show", methods="GET")
     */
    public function show(Reactivos $reactivo): Response
    {
        //checking the permissions
        $roles = $this->checkingPermission();
        //checking the permissions

        return $this->render('reactivos/show.html.twig', ['reactivo' => $reactivo, 'roles' => $roles, 'username'=>$_SESSION['fos_user_logged_name']]);
    }

    /**
     * @Route("/{id}/edit", name="reactivos_edit", methods="GET|POST")
     */
    public function edit(Request $request, Reactivos $reactivo): Response
    {
        //checking the permissions
        $roles = $this->checkingPermission();
        //checking the permissions

        $form = $this->createForm(ReactivosType::class, $reactivo);
        $form->handleRequest($request);

        $fisic_state = '';
        if(isset($request->request->all()['estado_fisico']))
            $fisic_state = $request->request->all()['estado_fisico'];//new

        if ($form->isSubmitted() && $form->isValid()) {
            $reactivo->setEstadoFisico($fisic_state);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reactivos_index', ['id' => $reactivo->getId()]);
        }

        return $this->render('reactivos/edit.html.twig', [
            'reactivo' => $reactivo,
            'form' => $form->createView(),
            'roles' => $roles,
            'username' => $_SESSION['fos_user_logged_name']
        ]);
    }

    /**
     * @Route("/{id}", name="reactivos_delete", methods="DELETE")
     */
    public function delete(Request $request, Reactivos $reactivo): Response
    {
        //checking the permissions
        if(!isset($_SESSION['fos_user_roles']))
            return $this->redirectToRoute('main');

        if($_SESSION['fos_user_roles']){
            $roles = $_SESSION['fos_user_roles'];
            $result = '';

            foreach ($roles as $value)
                if($value == 'ROLE_ADMIN' || $value == 'ROLE_ANALISTA_PRINCIPAL' || $value == 'ROLE_JEFE_EQUIPO')
                    $result = $value;

            if($result == '')
                return $this->redirectToRoute('main');
        }//checking the permissions

        if ($this->isCsrfTokenValid('delete'.$reactivo->getId(), $request->request->get('_token'))) {
            $solReactCantRepo = $this->getDoctrine()->getRepository(SolucionesReactivosCantidad::class);
            $em = $this->getDoctrine()->getManager();
            $result = $solReactCantRepo->findBy(['reactivos'=>$reactivo->getId()]);

            //eliminando en cascada, si no tendremos bugs, la relacion con las soluciones
            foreach ($result as $item)
                $em->remove($item);

            $em->remove($reactivo);
            $em->flush();
        }

        return $this->redirectToRoute('reactivos_index');
    }

    /**
     * @Route("/reactivos_add", name="reactivos_add")
     */
    public  function addAction(Request $request)
    {
        $amount = intval($request->request->get('addReactiveAmount'));
        $id = $request->request->get('addReactiveId');
        $unit = $request->request->get('ReactiveAddUnit');

        if(isset($amount) && isset($id) && isset($unit))
        {
            switch ($unit){
                case 'Gramos': {$amount = $amount/1000; break;}
                case 'Mililitros': {$amount = $amount/1000; break;}
                default: break;
            }

            $reactive = $this->getDoctrine()
                ->getRepository(Reactivos::class)
                ->find($id);

            $entityManager = $this->getDoctrine()->getManager();

            if(isset($reactive))
            {
                $currentAmount = intval($reactive->getCantidad());
                $newAmount = (int)$currentAmount + (int)$amount;

                if(is_int($newAmount) && is_int($currentAmount) && is_int($amount)){
                    $reactive->setCantidad($newAmount);
                    $entityManager->flush();
                }
                return $this->redirectToRoute('reactivos_index');
            }
        }
        return $this->redirectToRoute('reactivos_index');
    }

    /**
     * @Route("/reactivos_sub", name="reactivos_sub")
     */
    public  function subAction(Request $request)
    {
        $amount = intval($request->request->get('subReactiveIdAmount'));
        $id = intval($request->request->get('subReactiveId'));
        $unit = $request->request->get('ReactiveAddUnit');

        if(isset($amount) && isset($id) && isset($unit))
        {
            switch ($unit){
                case 'Gramos': {$amount = $amount/1000; break;}
                case 'Mililitros': {$amount = $amount/1000; break;}
                default: break;
            }

            $reactive = $this->getDoctrine()
                ->getRepository(Reactivos::class)
                ->find($id);

            $entityManager = $this->getDoctrine()->getManager();

            if(isset($reactive))
            {
                $currentAmount = $reactive->getCantidad();
                $limitAmount = $reactive->getCantidadMinima();
                $newAmount = $currentAmount - $amount;

                if($newAmount < 0){
                    $this->get('session')->getFlashBag()->add('warning', array('type' => 'danger', 'title' => 'Error', 'message' => 'No es posible substraer una cantidad mayor que la existente en el almacén'));
                    return $this->redirectToRoute('reactivos_index');
                }

                if($newAmount < $limitAmount){
                    $this->get('session')->getFlashBag()->add('warning', array('type' => 'danger', 'title' => 'Cuidado', 'message' => 'Limite de cantidad mínima'));

                    $reactive->setCantidad($newAmount);
                    $entityManager->flush();

                    return $this->redirectToRoute('reactivos_index');
                }

                $reactive->setCantidad($newAmount);
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute('reactivos_index');
    }

    /**
     * @Route("/react_search_by", name="react_search_by")
     */
    public  function searchByAction(Request $request)
    {
        $roles = $this->checkingPermission();

        $categoryToFilter = $request->request->all();

        if(count($categoryToFilter) == 0)
            return $this->redirectToRoute('reactivos_index');

        //the array result is an array of arrays
        $reactives = [];
        $categories = [];

        $em = $this->getDoctrine()->getManager();

        foreach ($categoryToFilter as $key => $value)
        {
            $sql1 = "SELECT reactivos_categoria.categoria_id as cid, Reactivos.id, Reactivos.nombre, Reactivos.nombre_quimico, Reactivos.formula, Reactivos.codigo_comercial, Reactivos.envase_comercial, Reactivos.cantidad, Reactivos.cantidad_minima
             FROM Reactivos INNER JOIN reactivos_categoria ON 
             Reactivos.id = reactivos_categoria.reactivos_id and reactivos_categoria.categoria_id = $value";

            $sql2 = "SELECT Reactivos.id as rid, Categoria.id as cid, Categoria.nombre
             FROM Reactivos INNER JOIN reactivos_categoria ON Reactivos.id = reactivos_categoria.reactivos_id AND reactivos_categoria.categoria_id = $value 
             LEFT JOIN Categoria ON Categoria.id = $value";

            $statement = $em->getConnection()->prepare($sql1);
            $statement->execute();
            $element1 = $statement->fetchAll();

            foreach ($element1 as $item)
                $reactives[$item['id']] = $item;

            $statement = $em->getConnection()->prepare($sql2);
            $statement->execute();
            $element2 = $statement->fetchAll();
            $categories[$element2[0]['nombre']] = $element2;
        }

        $repository = $this->getDoctrine()->getRepository(Categoria::class);
        $all_categoria = $repository->findAll();

        return $this->render('reactivos/search.html.twig', [
            'reactives' => $reactives,
            'roles' => $roles,
            'categories' => $categories,
            'username'=>$_SESSION['fos_user_logged_name'],
            'all_categoria' => $all_categoria]);
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
                if($value == 'ROLE_ADMIN' || $value == 'ROLE_ANALISTA_PRINCIPAL' || $value == 'ROLE_JEFE_EQUIPO')
                    $result = $value;

            if($result == '')
                return $this->redirectToRoute('main');
        }//checking the permissions(end)

        return $roles;
    }
}
