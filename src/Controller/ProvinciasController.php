<?php

namespace App\Controller;

use App\Entity\Provincias;
use App\Form\ProvinciasType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/provincias")
 */
class ProvinciasController extends AbstractController
{
    /**
     * @Route("/", name="provincias_index", methods={"GET"})
     */
    public function index(): Response
    {
        $provincias = $this->getDoctrine()
            ->getRepository(Provincias::class)
            ->findAll();

        return $this->render('provincias/index.html.twig', [
            'provincias' => $provincias,
        ]);
    }

    /**
     * @Route("/new", name="provincias_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $provincia = new Provincias();
        $form = $this->createForm(ProvinciasType::class, $provincia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($provincia);
            $entityManager->flush();

            return $this->redirectToRoute('provincias_index');
        }

        return $this->render('provincias/new.html.twig', [
            'provincia' => $provincia,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="provincias_show", methods={"GET"})
     */
    public function show(Provincias $provincia): Response
    {
        return $this->render('provincias/show.html.twig', [
            'provincia' => $provincia,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="provincias_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Provincias $provincia): Response
    {
        $form = $this->createForm(ProvinciasType::class, $provincia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('provincias_index');
        }

        return $this->render('provincias/edit.html.twig', [
            'provincia' => $provincia,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="provincias_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Provincias $provincia): Response
    {
        if ($this->isCsrfTokenValid('delete'.$provincia->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($provincia);
            $entityManager->flush();
        }

        return $this->redirectToRoute('provincias_index');
    }
}
