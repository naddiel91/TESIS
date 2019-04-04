<?php

namespace App\Controller;

use App\Entity\Metodo;
use App\Form\MetodoType;
use App\Repository\MetodoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/metodo")
 */
class MetodoController extends AbstractController
{
    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @Route("/", name="metodo_index", methods="GET")
     */
    public function index(MetodoRepository $metodoRepository, Request $request): Response
    {
        //checking the permissions
        $roles = $this->checkingPermission();
        //checking the permissions

        //knp paginator
        $paginator = $this->paginator;
        $metodos = $paginator->paginate(
            $metodoRepository->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );

        return $this->render('metodo/index.html.twig', ['metodos' => $metodos, 'roles' => $roles]);
    }

    /**
     * @Route("/new", name="metodo_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        //checking the permissions
        $roles = $this->checkingPermission();
        //checking the permissions

        $metodo = new Metodo();
        $form = $this->createForm(MetodoType::class, $metodo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($metodo);
            $em->flush();

            return $this->redirectToRoute('metodo_index');
        }

        return $this->render('metodo/new.html.twig', [
            'metodo' => $metodo,
            'form' => $form->createView(),
            'roles' => $roles
        ]);
    }

    /**
     * @Route("/{id}", name="metodo_show", methods="GET")
     */
    public function show(Metodo $metodo): Response
    {
        //checking the permissions
        $roles = $this->checkingPermission();
        //checking the permissions

        return $this->render('metodo/show.html.twig', ['metodo' => $metodo, 'roles' => $roles]);
    }

    /**
     * @Route("/{id}/edit", name="metodo_edit", methods="GET|POST")
     */
    public function edit(Request $request, Metodo $metodo): Response
    {
        //checking the permissions
        $roles = $this->checkingPermission();
        //checking the permissions

        $form = $this->createForm(MetodoType::class, $metodo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('metodo_index', ['id' => $metodo->getId()]);
        }

        return $this->render('metodo/edit.html.twig', [
            'metodo' => $metodo,
            'form' => $form->createView(),
            'roles' => $roles
        ]);
    }

    /**
     * @Route("/{id}", name="metodo_delete", methods="DELETE")
     */
    public function delete(Request $request, Metodo $metodo): Response
    {
        //checking the permissions
        $roles = $this->checkingPermission();
        //checking the permissions

        if ($this->isCsrfTokenValid('delete'.$metodo->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($metodo);
            $em->flush();
        }

        return $this->redirectToRoute('metodo_index');
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
