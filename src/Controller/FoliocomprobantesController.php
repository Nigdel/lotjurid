<?php

namespace App\Controller;

use App\Entity\Foliocomprobantes;
use App\Form\FoliocomprobantesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/foliocomprobantes")
 */
class FoliocomprobantesController extends AbstractController
{
    /**
     * @Route("/", name="foliocomprobantes_index", methods={"GET"})
     */
    public function index(): Response
    {
        $foliocomprobantes = $this->getDoctrine()
            ->getRepository(Foliocomprobantes::class)
            ->findAll();

        return $this->render('foliocomprobantes/index.html.twig', [
            'foliocomprobantes' => $foliocomprobantes,
        ]);
    }

    /**
     * @Route("/new", name="foliocomprobantes_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $foliocomprobante = new Foliocomprobantes();
        $form = $this->createForm(FoliocomprobantesType::class, $foliocomprobante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($foliocomprobante);
            $entityManager->flush();

            return $this->redirectToRoute('foliocomprobantes_index');
        }

        return $this->render('foliocomprobantes/new.html.twig', [
            'foliocomprobante' => $foliocomprobante,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{folio}", name="foliocomprobantes_show", methods={"GET"})
     */
    public function show(Foliocomprobantes $foliocomprobante): Response
    {
        return $this->render('foliocomprobantes/show.html.twig', [
            'foliocomprobante' => $foliocomprobante,
        ]);
    }

    /**
     * @Route("/{folio}/edit", name="foliocomprobantes_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Foliocomprobantes $foliocomprobante): Response
    {
        $form = $this->createForm(FoliocomprobantesType::class, $foliocomprobante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('foliocomprobantes_index');
        }

        return $this->render('foliocomprobantes/edit.html.twig', [
            'foliocomprobante' => $foliocomprobante,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{folio}", name="foliocomprobantes_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Foliocomprobantes $foliocomprobante): Response
    {
        if ($this->isCsrfTokenValid('delete'.$foliocomprobante->getFolio(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($foliocomprobante);
            $entityManager->flush();
        }

        return $this->redirectToRoute('foliocomprobantes_index');
    }
}
