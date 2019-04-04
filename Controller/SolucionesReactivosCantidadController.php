<?php

namespace App\Controller;

use App\Entity\SolucionesReactivosCantidad;
use App\Form\SolucionesReactivosCantidadType;
use App\Repository\SolucionesReactivosCantidadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/soluciones/reactivos/new")
 */
class SolucionesReactivosCantidadController extends AbstractController
{
//    /**
//     * @Route("/", name="soluciones_reactivos_new_index", methods="GET")
//     */
    public function index(SolucionesReactivosCantidadRepository $solucionesReactivosCantidadRepository): Response
    {
        return $this->render('soluciones_reactivos_new/index.html.twig', ['soluciones_reactivos_news' => $solucionesReactivosCantidadRepository->findAll()]);
    }

//    /**
//     * @Route("/new", name="soluciones_reactivos_new_new", methods="GET|POST")
//     */
    public function new(Request $request): Response
    {
        $solucionesReactivosNew = new SolucionesReactivosCantidad();
        $form = $this->createForm(SolucionesReactivosCantidadType::class, $solucionesReactivosNew);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($solucionesReactivosNew);
            $em->flush();

            return $this->redirectToRoute('soluciones_reactivos_new_index');
        }

        return $this->render('soluciones_reactivos_new/new.html.twig', [
            'soluciones_reactivos_new' => $solucionesReactivosNew,
            'form' => $form->createView(),
        ]);
    }

//    /**
//     * @Route("/{id}", name="soluciones_reactivos_new_show", methods="GET")
//     */
    public function show(SolucionesReactivosCantidad $solucionesReactivosNew): Response
    {
        return $this->render('soluciones_reactivos_new/show.html.twig', ['soluciones_reactivos_new' => $solucionesReactivosNew]);
    }

//    /**
//     * @Route("/{id}/edit", name="soluciones_reactivos_new_edit", methods="GET|POST")
//     */
    public function edit(Request $request, SolucionesReactivosCantidad $solucionesReactivosNew): Response
    {
        $form = $this->createForm(SolucionesReactivosCantidadType::class, $solucionesReactivosNew);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('soluciones_reactivos_new_index', ['id' => $solucionesReactivosNew->getId()]);
        }

        return $this->render('soluciones_reactivos_new/edit.html.twig', [
            'soluciones_reactivos_new' => $solucionesReactivosNew,
            'form' => $form->createView(),
        ]);
    }

//    /**
//     * @Route("/{id}", name="soluciones_reactivos_new_delete", methods="DELETE")
//     */
    public function delete(Request $request, SolucionesReactivosCantidad $solucionesReactivosNew): Response
    {
        if ($this->isCsrfTokenValid('delete'.$solucionesReactivosNew->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($solucionesReactivosNew);
            $em->flush();
        }

        return $this->redirectToRoute('soluciones_reactivos_new_index');
    }
}
