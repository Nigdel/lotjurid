<?php

namespace App\Controller;

use App\Entity\Municipios;
use App\Form\MunicipiosType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/municipios")
 */
class MunicipiosController extends AbstractController
{
    /**
     * @Route("/", name="municipios_index", methods={"GET"})
     */
    public function index(): Response
    {
        $municipios = $this->getDoctrine()
            ->getRepository(Municipios::class)
            ->findAll();

        return $this->render('municipios/index.html.twig', [
            'municipios' => $municipios,
        ]);
    }

    /**
     * @Route("/new", name="municipios_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $municipio = new Municipios();
        $form = $this->createForm(MunicipiosType::class, $municipio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($municipio);
            $entityManager->flush();

            return $this->redirectToRoute('municipios_index');
        }

        return $this->render('municipios/new.html.twig', [
            'municipio' => $municipio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="municipios_show", methods={"GET"})
     */
    public function show(Municipios $municipio): Response
    {
        return $this->render('municipios/show.html.twig', [
            'municipio' => $municipio,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="municipios_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Municipios $municipio): Response
    {
        $form = $this->createForm(MunicipiosType::class, $municipio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('municipios_index');
        }

        return $this->render('municipios/edit.html.twig', [
            'municipio' => $municipio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="municipios_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Municipios $municipio): Response
    {
        if ($this->isCsrfTokenValid('delete'.$municipio->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($municipio);
            $entityManager->flush();
        }

        return $this->redirectToRoute('municipios_index');
    }
}
