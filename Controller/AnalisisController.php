<?php

namespace App\Controller;

use App\Entity\Analisis;
use App\Form\AnalisisType;
use App\Repository\AnalisisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/analisis")
 */
class AnalisisController extends AbstractController
{
    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @Route("/", name="analisis_index", methods="GET")
     */
    public function index(AnalisisRepository $analisisRepository, Request $request): Response
    {
        //checking the permissions(start)
        $roles = $this->checkingPermission();
        //checking the permissions(end)

        //knp paginator
        $paginator = $this->paginator;
        $analisis = $paginator->paginate(
            $analisisRepository->findAll(),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );

        return $this->render('analisis/index.html.twig', ['analises' => $analisis, 'roles' => $roles]);
    }

    /**
     * @Route("/new", name="analisis_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        //checking the permissions
        $roles = $this->checkingPermission();
        //checking the permissions

        $analisi = new Analisis();
        $form = $this->createForm(AnalisisType::class, $analisi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($analisi);
            $em->flush();

            return $this->redirectToRoute('analisis_index');
        }

        return $this->render('analisis/new.html.twig', [
            'analisi' => $analisi,
            'form' => $form->createView(),
            'roles' => $roles
        ]);
    }

    /**
     * @Route("/{id}", name="analisis_show", methods="GET")
     */
    public function show(Analisis $analisi): Response
    {
        //checking the permissions(start)
        $roles = $this->checkingPermission();
        //checking the permissions(end)

        return $this->render('analisis/show.html.twig', ['analisi' => $analisi, 'roles' => $roles]);
    }

    /**
     * @Route("/{id}/edit", name="analisis_edit", methods="GET|POST")
     */
    public function edit(Request $request, Analisis $analisi): Response
    {
        //checking the permissions
        $roles = $this->checkingPermission();
        //checking the permissions

        $form = $this->createForm(AnalisisType::class, $analisi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('analisis_index', ['id' => $analisi->getId()]);
        }

        return $this->render('analisis/edit.html.twig', [
            'analisi' => $analisi,
            'form' => $form->createView(),
            'roles' => $roles
        ]);
    }

    /**
     * @Route("/{id}", name="analisis_delete", methods="DELETE")
     */
    public function delete(Request $request, Analisis $analisi): Response
    {
        //checking the permissions
        $roles = $this->checkingPermission();
        //checking the permissions

        if ($this->isCsrfTokenValid('delete'.$analisi->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($analisi);
            $em->flush();
        }

        return $this->redirectToRoute('analisis_index');
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
