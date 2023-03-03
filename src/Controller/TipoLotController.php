<?php

namespace App\Controller;

use App\Entity\TipoLot;
use App\Form\TipoLotType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tipo/lot")
 */
class TipoLotController extends AbstractController
{
    /**
     * @Route("/", name="tipo_lot_index", methods={"GET"})
     */
    public function index(): Response
    {
        $tipoLots = $this->getDoctrine()
            ->getRepository(TipoLot::class)
            ->findAll();

        return $this->render('tipo_lot/index.html.twig', [
            'tipo_lots' => $tipoLots,
        ]);
    }

    /**
     * @Route("/new", name="tipo_lot_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $tipoLot = new TipoLot();
        $form = $this->createForm(TipoLotType::class, $tipoLot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tipoLot);
            $entityManager->flush();

            return $this->redirectToRoute('tipo_lot_index');
        }

        return $this->render('tipo_lot/new.html.twig', [
            'tipo_lot' => $tipoLot,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tipo_lot_show", methods={"GET"})
     */
    public function show(TipoLot $tipoLot): Response
    {
        return $this->render('tipo_lot/show.html.twig', [
            'tipo_lot' => $tipoLot,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tipo_lot_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TipoLot $tipoLot): Response
    {
        $form = $this->createForm(TipoLotType::class, $tipoLot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tipo_lot_index');
        }

        return $this->render('tipo_lot/edit.html.twig', [
            'tipo_lot' => $tipoLot,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tipo_lot_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TipoLot $tipoLot): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tipoLot->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tipoLot);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tipo_lot_index');
    }
}
