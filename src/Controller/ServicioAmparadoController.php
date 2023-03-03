<?php

namespace App\Controller;

use App\Entity\ServicioAmparado;
use App\Form\ServicioAmparadoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/servicio/amparado")
 */
class ServicioAmparadoController extends AbstractController
{
    /**
     * @Route("/", name="servicio_amparado_index", methods={"GET"})
     */
    public function index(): Response
    {
        $servicioAmparados = $this->getDoctrine()
            ->getRepository(ServicioAmparado::class)
            ->findAll();

        return $this->render('servicio_amparado/index.html.twig', [
            'servicio_amparados' => $servicioAmparados,
        ]);
    }

    /**
     * @Route("/new", name="servicio_amparado_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $servicioAmparado = new ServicioAmparado();
        $form = $this->createForm(ServicioAmparadoType::class, $servicioAmparado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($servicioAmparado);
            $entityManager->flush();

            return $this->redirectToRoute('servicio_amparado_index');
        }

        return $this->render('servicio_amparado/new.html.twig', [
            'servicio_amparado' => $servicioAmparado,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="servicio_amparado_show", methods={"GET"})
     */
    public function show(ServicioAmparado $servicioAmparado): Response
    {
        return $this->render('servicio_amparado/show.html.twig', [
            'servicio_amparado' => $servicioAmparado,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="servicio_amparado_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ServicioAmparado $servicioAmparado): Response
    {
        $form = $this->createForm(ServicioAmparadoType::class, $servicioAmparado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('servicio_amparado_index');
        }

        return $this->render('servicio_amparado/edit.html.twig', [
            'servicio_amparado' => $servicioAmparado,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="servicio_amparado_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ServicioAmparado $servicioAmparado): Response
    {
        if ($this->isCsrfTokenValid('delete'.$servicioAmparado->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($servicioAmparado);
            $entityManager->flush();
        }

        return $this->redirectToRoute('servicio_amparado_index');
    }
}
