<?php

namespace App\Controller;

use App\Entity\Ramas;
use App\Form\RamasType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ramas")
 */
class RamasController extends AbstractController
{
    /**
     * @Route("/", name="ramas_index", methods={"GET"})
     */
    public function index(): Response
    {
        $ramas = $this->getDoctrine()
            ->getRepository(Ramas::class)
            ->findAll();

        return $this->render('ramas/index.html.twig', [
            'ramas' => $ramas,
        ]);
    }

    /**
     * @Route("/new", name="ramas_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $rama = new Ramas();
        $form = $this->createForm(RamasType::class, $rama);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rama);
            $entityManager->flush();

            return $this->redirectToRoute('ramas_index');
        }

        return $this->render('ramas/new.html.twig', [
            'rama' => $rama,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ramas_show", methods={"GET"})
     */
    public function show(Ramas $rama): Response
    {
        return $this->render('ramas/show.html.twig', [
            'rama' => $rama,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ramas_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ramas $rama): Response
    {
        $form = $this->createForm(RamasType::class, $rama);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ramas_index');
        }

        return $this->render('ramas/edit.html.twig', [
            'rama' => $rama,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ramas_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Ramas $rama): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rama->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($rama);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ramas_index');
    }
}
