<?php

namespace App\Controller;

use App\Entity\Tramite;
use App\Form\TramiteType;
use App\Repository\EstadoTramiteRepository;
use App\Repository\TipotramiteRepository;
use App\Repository\TramiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tramite")
 */
class TramiteController extends AbstractController
{
    /**
     * @Route("/", name="tramite_index", methods={"GET"})
     */
    public function index(TramiteRepository $tramiteRepository): Response
    {
        return $this->render('tramite/index.html.twig', [
            'tramites' => $tramiteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="tramite_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $tramite = new Tramite();
        $form = $this->createForm(TramiteType::class, $tramite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tramite);
            $entityManager->flush();

            return $this->redirectToRoute('tramite_index');
        }

        return $this->render('tramite/new.html.twig', [
            'tramite' => $tramite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/newpj", name="tramite_newpj")
     */
    public function trNewPj(TipotramiteRepository $tt, EstadoTramiteRepository $et){
        $tramite= new Tramite();
        $tramite->setTipotramite($tt->find(2));
        $tramite->setUsuario($this->getUser());
        $tramite->setFecha( new \DateTime('today'));
        $tramite->setEstado($et->find(1));

        return $this->redirectToRoute('personasjuridicas_new',['tramite'=>$tramite]);
    }




    /**
     * @Route("/{id}", name="tramite_show", methods={"GET"})
     */
    public function show(Tramite $tramite): Response
    {
        return $this->render('tramite/show.html.twig', [
            'tramite' => $tramite,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tramite_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tramite $tramite): Response
    {
        $form = $this->createForm(TramiteType::class, $tramite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tramite_index');
        }

        return $this->render('tramite/edit.html.twig', [
            'tramite' => $tramite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tramite_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tramite $tramite): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tramite->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tramite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tramite_index');
    }


}
