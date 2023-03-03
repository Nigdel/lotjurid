<?php

namespace App\Controller;

use App\Entity\TipoServicio;
use App\Form\TipoServicioType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tipo/servicio")
 */
class TipoServicioController extends AbstractController
{
    /**
     * @Route("/", name="tipo_servicio_index", methods={"GET"})
     */
    public function index(): Response
    {
        $tipoServicios = $this->getDoctrine()
            ->getRepository(TipoServicio::class)
            ->findAll();

        return $this->render('tipo_servicio/index.html.twig', [
            'tipo_servicios' => $tipoServicios,
        ]);
    }

    /**
     * @Route("/new", name="tipo_servicio_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $tipoServicio = new TipoServicio();
        $form = $this->createForm(TipoServicioType::class, $tipoServicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tipoServicio);
            $entityManager->flush();

            return $this->redirectToRoute('tipo_servicio_index');
        }

        return $this->render('tipo_servicio/new.html.twig', [
            'tipo_servicio' => $tipoServicio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tipo_servicio_show", methods={"GET"})
     */
    public function show(TipoServicio $tipoServicio): Response
    {
        return $this->render('tipo_servicio/show.html.twig', [
            'tipo_servicio' => $tipoServicio,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tipo_servicio_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TipoServicio $tipoServicio): Response
    {
        $form = $this->createForm(TipoServicioType::class, $tipoServicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tipo_servicio_index');
        }

        return $this->render('tipo_servicio/edit.html.twig', [
            'tipo_servicio' => $tipoServicio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tipo_servicio_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TipoServicio $tipoServicio): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tipoServicio->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tipoServicio);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tipo_servicio_index');
    }
}
