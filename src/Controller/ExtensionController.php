<?php

namespace App\Controller;

use App\Entity\Extension;
use App\Form\ExtensionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/extension")
 */
class ExtensionController extends AbstractController
{
    /**
     * @Route("/", name="extension_index", methods={"GET"})
     */
    public function index(): Response
    {
        $extensions = $this->getDoctrine()
            ->getRepository(Extension::class)
            ->findAll();

        return $this->render('extension/index.html.twig', [
            'extensions' => $extensions,
        ]);
    }

    /**
     * @Route("/new", name="extension_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $extension = new Extension();
        $form = $this->createForm(ExtensionType::class, $extension);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($extension);
            $entityManager->flush();

            return $this->redirectToRoute('extension_index');
        }

        return $this->render('extension/new.html.twig', [
            'extension' => $extension,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="extension_show", methods={"GET"})
     */
    public function show(Extension $extension): Response
    {
        return $this->render('extension/show.html.twig', [
            'extension' => $extension,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="extension_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Extension $extension): Response
    {
        $form = $this->createForm(ExtensionType::class, $extension);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('extension_index');
        }

        return $this->render('extension/edit.html.twig', [
            'extension' => $extension,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="extension_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Extension $extension): Response
    {
        if ($this->isCsrfTokenValid('delete'.$extension->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($extension);
            $entityManager->flush();
        }

        return $this->redirectToRoute('extension_index');
    }
}
