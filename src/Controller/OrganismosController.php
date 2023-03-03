<?php

namespace App\Controller;

use App\Entity\Organismos;
use App\Form\OrganismosType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/organismos")
 */
class OrganismosController extends AbstractController
{
    /**
     * @Route("/", name="organismos_index", methods={"GET"})
     */
    public function index(): Response
    {
        $organismos = $this->getDoctrine()
            ->getRepository(Organismos::class)
            ->findAll();

        return $this->render('organismos/index.html.twig', [
            'organismos' => $organismos,
        ]);
    }

    /**
     * @Route("/new", name="organismos_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $organismo = new Organismos();
        $form = $this->createForm(OrganismosType::class, $organismo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($organismo);
            $entityManager->flush();

            return $this->redirectToRoute('organismos_index');
        }

        return $this->render('organismos/new.html.twig', [
            'organismo' => $organismo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{Cod}", name="organismos_show", methods={"GET"})
     */
    public function show(Organismos $organismo): Response
    {
        return $this->render('organismos/show.html.twig', [
            'organismo' => $organismo,
        ]);
    }

    /**
     * @Route("/{Cod}/edit", name="organismos_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Organismos $organismo): Response
    {
        $form = $this->createForm(OrganismosType::class, $organismo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('organismos_index');
        }

        return $this->render('organismos/edit.html.twig', [
            'organismo' => $organismo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{Cod}", name="organismos_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Organismos $organismo): Response
    {
        if ($this->isCsrfTokenValid('delete'.$organismo->getCod(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($organismo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('organismos_index');
    }
}
