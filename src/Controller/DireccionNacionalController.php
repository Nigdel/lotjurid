<?php

namespace App\Controller;

use App\Entity\DireccionNacional;
use App\Form\DireccionNacionalType;
use App\Repository\DireccionNacionalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/direccionnacional")
 */
class DireccionNacionalController extends AbstractController
{
    /**
     * @Route("/", name="direccion_nacional_index", methods={"GET"})
     */
    public function index(DireccionNacionalRepository $direccionNacionalRepository): Response
    {
      $dir= $direccionNacionalRepository->find(1);
      $provs= $dir->getDireccionesProvinciales();
      return $this->render('direccion_nacional/index.html.twig',['dir'=>$dir,'provs'=>$provs->toArray()]);

 //        return $this->render('direccion_nacional/index.html.twig', [
//            'direccion_nacionals' => $direccionNacionalRepository->findAll(),
//        ]);
    }

    /**
     * @Route("/new", name="direccion_nacional_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $direccionNacional = new DireccionNacional();
        $form = $this->createForm(DireccionNacionalType::class, $direccionNacional);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($direccionNacional);
            $entityManager->flush();

            return $this->redirectToRoute('direccion_nacional_index');
        }

        return $this->render('direccion_nacional/new.html.twig', [
            'direccion_nacional' => $direccionNacional,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="direccion_nacional_show", methods={"GET"})
     */
    public function show(DireccionNacional $direccionNacional): Response
    {
        return $this->render('direccion_nacional/show.html.twig', [
            'direccion_nacional' => $direccionNacional,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="direccion_nacional_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, DireccionNacional $direccionNacional): Response
    {
        $form = $this->createForm(DireccionNacionalType::class, $direccionNacional);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('direccion_nacional_index');
        }

        return $this->render('direccion_nacional/edit.html.twig', [
            'direccion_nacional' => $direccionNacional,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="direccion_nacional_delete", methods={"DELETE"})
     */
    public function delete(Request $request, DireccionNacional $direccionNacional): Response
    {
        if ($this->isCsrfTokenValid('delete'.$direccionNacional->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($direccionNacional);
            $entityManager->flush();
        }

        return $this->redirectToRoute('direccion_nacional_index');
    }
}
