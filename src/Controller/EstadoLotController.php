<?php

namespace App\Controller;

use App\Entity\EstadoLot;
use App\Form\EstadoLotType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/estado/lot")
 */
class EstadoLotController extends AbstractController
{
    /**
     * @Route("/", name="estado_lot_index", methods={"GET"})
     */
    public function index(): Response
    {
        $estadoLots = $this->getDoctrine()
            ->getRepository(EstadoLot::class)
            ->findAll();

        return $this->render('estado_lot/index.html.twig', [
            'estado_lots' => $estadoLots,
        ]);
    }

    /**
     * @Route("/new", name="estado_lot_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $estadoLot = new EstadoLot();
        $form = $this->createForm(EstadoLotType::class, $estadoLot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($estadoLot);
            $entityManager->flush();

            return $this->redirectToRoute('estado_lot_index');
        }

        return $this->render('estado_lot/new.html.twig', [
            'estado_lot' => $estadoLot,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="estado_lot_show", methods={"GET"})
     */
    public function show(EstadoLot $estadoLot): Response
    {
        return $this->render('estado_lot/show.html.twig', [
            'estado_lot' => $estadoLot,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="estado_lot_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EstadoLot $estadoLot): Response
    {
        $form = $this->createForm(EstadoLotType::class, $estadoLot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('estado_lot_index');
        }

        return $this->render('estado_lot/edit.html.twig', [
            'estado_lot' => $estadoLot,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="estado_lot_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EstadoLot $estadoLot): Response
    {
        if ($this->isCsrfTokenValid('delete'.$estadoLot->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($estadoLot);
            $entityManager->flush();
        }

        return $this->redirectToRoute('estado_lot_index');
    }
}
