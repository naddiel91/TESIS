<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Form\CategoriaType;
use App\Repository\CategoriaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/categoria")
 */
class CategoriaController extends AbstractController
{
    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @Route("/", name="categoria_index", methods="GET")
     */
    public function index(CategoriaRepository $categoriaRepository, Request $request): Response
    {
        //checking the permissions
        $roles = $this->checkingPermission();
        //checking the permissions

        //knp paginator
        $paginator = $this->paginator;
        $categoria = $paginator->paginate(
            $categoriaRepository->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );

        return $this->render('categoria/index.html.twig', ['categorias' => $categoria, 'roles' => $roles]);
    }

    /**
     * @Route("/new", name="categoria_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        //checking the permissions
        $roles = $this->checkingPermission();
        //checking the permissions

        $categorium = new Categoria();
        $form = $this->createForm(CategoriaType::class, $categorium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($categorium);
            $em->flush();

            return $this->redirectToRoute('categoria_index');
        }

        return $this->render('categoria/new.html.twig', [
            'categorium' => $categorium,
            'form' => $form->createView(),
            'roles' => $roles
        ]);
    }

    /**
     * @Route("/{id}", name="categoria_show", methods="GET")
     */
    public function show(Categoria $categorium): Response
    {
        //checking the permissions
        $roles = $this->checkingPermission();
        //checking the permissions

        return $this->render('categoria/show.html.twig', ['categorium' => $categorium, 'roles' => $roles]);
    }

    /**
     * @Route("/{id}/edit", name="categoria_edit", methods="GET|POST")
     */
    public function edit(Request $request, Categoria $categorium): Response
    {
        //checking the permissions
        $roles = $this->checkingPermission();
        //checking the permissions

        $form = $this->createForm(CategoriaType::class, $categorium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('categoria_index', ['id' => $categorium->getId(), 'roles' => $roles]);
        }

        return $this->render('categoria/edit.html.twig', [
            'categorium' => $categorium,
            'form' => $form->createView(),
            'roles' => $roles
        ]);
    }

    /**
     * @Route("/{id}", name="categoria_delete", methods="DELETE")
     */
    public function delete(Request $request, Categoria $categorium): Response
    {
        //checking the permissions
        $roles = $this->checkingPermission();
        //checking the permissions

        if ($this->isCsrfTokenValid('delete'.$categorium->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($categorium);
            $em->flush();
        }

        return $this->redirectToRoute('categoria_index');
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
