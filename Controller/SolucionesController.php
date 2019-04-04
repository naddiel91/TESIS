<?php

namespace App\Controller;

use App\Entity\Soluciones;
use App\Entity\Analisis;
use App\Entity\Reactivos;
use App\Entity\SolucionesReactivosCantidad;
use App\Form\SolucionesType;
use App\Repository\SolucionesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/soluciones")
 */
class SolucionesController extends AbstractController
{
    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @Route("/", name="soluciones_index", methods="GET")
     */
    public function index(SolucionesRepository $solucionesRepository, Request $request): Response
    {
        //checking the permissions(start)
        $roles = $this->checkingPermissions();
        //checking the permissions(end)

        $repository = $this->getDoctrine()->getRepository(Analisis::class);
        $all_analisis = $repository->findAll();

        $repository = $this->getDoctrine()->getRepository(Reactivos::class);
        $all_reactivos = $repository->findAll();

        //knp paginator
        $paginator = $this->paginator;
        $soluciones = $paginator->paginate(
            $solucionesRepository->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );

        return $this->render('soluciones/index.html.twig', [
            'soluciones' => $soluciones,
            'roles' => $roles,
            'username'=> $_SESSION['fos_user_logged_name'],
            'all_analisis' => $all_analisis,
            'all_reactivos' => $all_reactivos]);
    }

    /**
     * @Route("/new", name="soluciones_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        //checking the permissions(start)
        $roles = $this->checkingPermissions();
        //checking the permissions(end)

        $solucione = new Soluciones();
        $form = $this->createForm(SolucionesType::class, $solucione);
        $form->handleRequest($request);

        $arrayInputs = $request->request->all();
        $repository = $this->getDoctrine()->getRepository(Reactivos::class);
        $unit = '';
        $reactive = null;
        $em = $this->getDoctrine()->getManager();

        //agregando reactivos a la solucion
        if(isset($arrayInputs) && count($arrayInputs) > 0){
            foreach ($arrayInputs as $key => $value){
                $key = str_replace('_',' ', $key);

                $pos = (strpos($key,'react'));
                if($pos !== false){
                    $reactive = $repository->find($value);
                    $solucione->addReactivo($reactive);
                }

                $pos = strpos($key,'unit');

                if($pos !== false){
                    $unit = $value;
                }

                $pos = strpos($key,'amount');
                if($pos !== false){
                    switch ($unit){
                        case 'kg': { $value = intval($value) * 1000; break; }
                        case 'l': { $value = intval($value) * 1000; break; }
                        default : break;
                    }
                    //rebajar reactivo del almacen, si no se puede lanzar excepcion con un flash
                    if(!$this->reduceReactivesFromStorage($reactive, $value))
                        return $this->redirectToRoute('soluciones_index');

                    $solReact = new SolucionesReactivosCantidad();
                    $solReact->setSoluciones($solucione);
                    $solReact->setReactivos($reactive);
                    $solReact->setCantidadReactivo($value);
                    $em->persist($solReact);
                }

            }
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $solucione->setAnalista($_SESSION['fos_user_logged_name']);
            $solucione->setFechaCreada($request->request->get('dateTimeSolFC'));
            $solucione->setFechaVencimiento($request->request->get('dateTimeSolFV'));

            $em->persist($solucione);
            $em->flush();

            return $this->redirectToRoute('soluciones_index');
        }

        $all_reactivos = $repository->findAll();

        return $this->render('soluciones/new.html.twig', [
            'solucione' => $solucione,
            'form' => $form->createView(),
            'roles' => $roles,
            'username'=> $_SESSION['fos_user_logged_name'],
            'all_reactivos' => $all_reactivos,
            'reactivos' => ''
        ]);
    }

    /**
     * @Route("/{id}", name="soluciones_show", methods="GET")
     */
    public function show(Soluciones $solucione): Response
    {
        //checking the permissions(start)
        $roles = $this->checkingPermissions();
        //checking the permissions(end)

        return $this->render('soluciones/show.html.twig', ['solucione' => $solucione, 'roles' => $roles,'username'=> $_SESSION['fos_user_logged_name']]);
    }

    /**
     * @Route("/{id}/edit", name="soluciones_edit", methods="GET|POST")
     */
    public function edit(Request $request, Soluciones $solucione): Response
    {
        //checking the permissions(start)
        $roles = $this->checkingPermissions();
        //checking the permissions(end)

        $form = $this->createForm(SolucionesType::class, $solucione);
        $form->handleRequest($request);

        $repository = $this->getDoctrine()->getRepository(Reactivos::class);
        $solReactRepo = $this->getDoctrine()->getRepository(SolucionesReactivosCantidad::class);

        //reactivos asociados a la solucion...
        $id = $solucione->getId();
        $em = $this->getDoctrine()->getManager();
        $sql2 = "SELECT Reactivos.id as rid, Reactivos.nombre as rnombre, soluciones_reactivos_cantidad.cantidad_reactivo as cantidad
                 FROM Reactivos INNER JOIN soluciones_reactivos_cantidad ON Reactivos.id = soluciones_reactivos_cantidad.reactivos_id AND soluciones_reactivos_cantidad.soluciones_id = $id";
        $statement = $em->getConnection()->prepare($sql2);
        $statement->execute();
        $reactivos = $statement->fetchAll();//reactivos asociados a la solucion
        $reactive = null;

        if ($form->isSubmitted() && $form->isValid()) {
            //editando los reactivos de la solucion...adding
            $formToEdit = $request->request->all();

            foreach ($formToEdit as $key=>$value){
                $key = str_replace('_',' ', $key);

                $pos = (strpos($key,'react'));
                if($pos !== false){
                    $reactive = $repository->find($value);
                }

                $pos = strpos($key,'unit');
                if($pos !== false){
                    $unit = $value;
                }

                $pos = strpos($key,'amount');
                if($pos !== false){
                    switch ($unit){
                        case 'g': { $value = intval($value) / 1000; break; }
                        case 'ml': { $value = intval($value) / 1000; break; }
                        default : break;
                    }
                    //rebajar reactivo del almacen, si no se puede lanzar excepcion con un flash
                    if(!$this->reduceReactivesFromStorage($reactive, $value))
                        return $this->redirectToRoute('soluciones_index');

                    //aqui es donde se busca el reactivo y se actualiza o se relaciona <-OJO
                    if(isset($reactive) && !$this->editingSolutionAndReactivesOnSolReactCantEntity($solucione, $reactive, $value)){
                        $solucione->addReactivo($reactive);
                        $solReact = new SolucionesReactivosCantidad();
                        $solReact->setSoluciones($solucione);
                        $solReact->setReactivos($reactive);
                        $solReact->setCantidadReactivo($value);
                        $em->persist($solReact);
                        $em->persist($solucione);
                    }
                }
            }
            //usuario logeado que edita la solucion, medida de seguridad, se realiza automaticamente
            $solucione->setAnalista($_SESSION['fos_user_logged_name']);
            $solucione->setFechaCreada($request->request->get('dateTimeSolFC'));
            $solucione->setFechaVencimiento($request->request->get('dateTimeSolFV'));

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('soluciones_index', ['id' => $solucione->getId()]);
        }

        $all_reactivos = $repository->findAll();

//        var_dump($reactivos);die();

        return $this->render('soluciones/edit.html.twig', [
            'solucione' => $solucione,
            'form' => $form->createView(),
            'roles' => $roles,
            'username'=> $_SESSION['fos_user_logged_name'],
            'all_reactivos' => $all_reactivos,
            'reactivos' => $reactivos
        ]);
    }

    /**
     * @Route("/{id}", name="soluciones_delete", methods="DELETE")
     */
    public function delete(Request $request, Soluciones $solucione): Response
    {
        //checking the permissions(start)
        $roles = $this->checkingPermissions();
        //checking the permissions(end)

        if ($this->isCsrfTokenValid('delete'.$solucione->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();

            $repository = $this->getDoctrine()->getRepository(SolucionesReactivosCantidad::class);
            $criteria = array('soluciones'=>$solucione->getId());
            $sol = $repository->findBy($criteria);

            //soluciones asociadas a solucionesReactivosNew
            foreach ($sol as $item) {
                $em->remove($item);
            }

            $em->remove($solucione);
            $em->flush();
        }

        return $this->redirectToRoute('soluciones_index');
    }

    /**
     * @Route("/search_by", name="search_by")
     */
    public  function searchByAction(Request $request)
    {
        //checking the permissions(start)
        $roles = $this->checkingPermissions();
        //checking the permissions(end)

        $analisis_reactivos = $request->request->all();

        if(count($analisis_reactivos) == 0)
            return $this->redirectToRoute('soluciones_index');

        //the array result is an array of arrays
        $result = [];

        $em = $this->getDoctrine()->getManager();

        foreach ($analisis_reactivos as $key => $value)
        {
            $key = str_replace('_',' ', $key);
            $pos = (strpos($key,'analisis'));

            //analisis match
            if($pos !== false){
                $sql1 = "SELECT Soluciones.id, Soluciones.nombre, Soluciones.descripcion, Analisis.nombre as analisis, Soluciones.analista, Soluciones.fecha_creada, Soluciones.fecha_vencimiento 
                  FROM Soluciones INNER JOIN Analisis ON 
                  Soluciones.analisis_id = Analisis.id and Analisis.id = $value";

                $statement = $em->getConnection()->prepare($sql1);
                $statement->execute();
                $element1 = $statement->fetchAll();

                if(count($element1) > 0)
                    $result[] = $element1;//array de array
            }

            $pos = (strpos($key,'react'));

            //reactivos match
            if($pos !== false){
                $sql2 = "SELECT Soluciones.id, Soluciones.nombre, Soluciones.descripcion, Analisis.nombre as analisis,Soluciones.analista, Soluciones.fecha_creada, Soluciones.fecha_vencimiento
                 FROM Soluciones INNER JOIN soluciones_reactivos ON Soluciones.id = soluciones_reactivos.soluciones_id AND soluciones_reactivos.reactivos_id = $value
                 INNER JOIN Analisis ON Soluciones.analisis_id = Analisis.id";

                $statement = $em->getConnection()->prepare($sql2);
                $statement->execute();
                $element2 = $statement->fetchAll();

                if(count($element2) > 0)
                    $result[] = $element2;//array de array

            }
        }

        $repository = $this->getDoctrine()->getRepository(Analisis::class);
        $all_analisis = $repository->findAll();

        $repository = $this->getDoctrine()->getRepository(Reactivos::class);
        $all_reactivos = $repository->findAll();

        return $this->render('soluciones/search.html.twig', [
            'soluciones' => $result,
            'roles' => $roles,
            'username'=> $_SESSION['fos_user_logged_name'],
            'all_analisis' => $all_analisis,
            'all_reactivos' => $all_reactivos]);
    }

    /**
     * $solReactNew son las ocurrencias de la solucion x en la entidad SolucionesReactivosCantidad
     * retorna verdadero si ya existia el reactivo y falso en caso contrario y solo se actualizaria la cantidad
    */
    private function editingSolutionAndReactivesOnSolReactCantEntity(Soluciones $solucion, Reactivos $reactivo, $cantidad)
    {
        $em = $this->getDoctrine()->getManager();
        $solReactCantRepo = $this->getDoctrine()->getRepository(SolucionesReactivosCantidad::class);
        $result = $solReactCantRepo->findOneBy(['soluciones'=>$solucion, 'reactivos'=>$reactivo]);

        if(isset($result)){
            $cantidadActual = $result->getCantidadReactivo();
            $result->setCantidadReactivo($cantidadActual+$cantidad);
            $em->persist($result);
            $em->flush();
            return true;
        }
        return false;
    }

    /**
     * este metodo rebaja del alamacen de reactivos la cantidad de reactivo x usada en la solucion y
     */
    private function reduceReactivesFromStorage(Reactivos $reactivo, $cantidad)
    {
        $em = $this->getDoctrine()->getManager();

        if((int)$reactivo->getCantidad() < (int)$cantidad){
            $this->get('session')->getFlashBag()->add('error', array('type' => 'error', 'title' => 'Error', 'message' => 'Insuficiente cantidad de < '.$reactivo->getNombre().' > para preparar la solución'));
            return false;
        }

        else if((int)$reactivo->getCantidad() >= (int)$cantidad){
            $cantidadActual = (int)$reactivo->getCantidad() - (int)$cantidad;
            $reactivo->setCantidad($cantidadActual);

            if((int)$reactivo->getCantidad() <= (int)$reactivo->getCantidadMinima())
                $this->get('session')->getFlashBag()->add('warning', array('type' => 'warning','title' => 'Cuidado', 'message' => 'Cantidad mínima de < '.$reactivo->getNombre().' > alcanzada'));

            $em->persist($reactivo);
            $em->flush();
            return true;
        }

        return false;
    }


    /**
     * chequear los permisos
     */
    private function checkingPermissions()
    {
        $roles = null;

        if(!isset($_SESSION['fos_user_roles']))
            return $this->redirectToRoute('main');

        if($_SESSION['fos_user_roles']){
            $roles = $_SESSION['fos_user_roles'];
            $result = '';

            foreach ($roles as $value)
                if($value == 'ROLE_ADMIN' || $value == 'ROLE_ANALISTA_PRINCIPAL' || $value == 'ROLE_JEFE_EQUIPO' || $value == 'ROLE_ANALISTA')
                    $result = $value;

            if($result == '')
                return $this->redirectToRoute('main');
        }
       return $roles;
    }
}
