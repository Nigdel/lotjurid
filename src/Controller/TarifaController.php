<?php

namespace App\Controller;

use App\Entity\Tarifa;
use App\Form\TarifaType;
use App\Repository\TarifaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tarifa")
 */
class TarifaController extends AbstractController
{
    /**
     * @Route("/", name="tarifa_index", methods={"GET"})
     */
    public function index(TarifaRepository $tarifaRepository): Response
    {
        return $this->render('tarifa/index.html.twig', [
            'tarifas' => $tarifaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="tarifa_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $tarifa = new Tarifa();
        $form = $this->createForm(TarifaType::class, $tarifa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tarifa);
            $entityManager->flush();

            return $this->redirectToRoute('tarifa_index');
        }

        return $this->render('tarifa/new.html.twig', [
            'tarifa' => $tarifa,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tarifa_show", methods={"GET"})
     */
    public function show(Tarifa $tarifa): Response
    {
        return $this->render('tarifa/show.html.twig', [
            'tarifa' => $tarifa,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tarifa_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tarifa $tarifa): Response
    {
        $form = $this->createForm(TarifaType::class, $tarifa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tarifa_index');
        }

        return $this->render('tarifa/edit.html.twig', [
            'tarifa' => $tarifa,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tarifa_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tarifa $tarifa): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tarifa->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tarifa);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tarifa_index');
    }
}
