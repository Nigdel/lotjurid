<?php

namespace App\Controller;

use App\Entity\Mensaje;
use App\Form\MensajeType;
use App\Repository\EstadoMensajeRepository;
use App\Repository\MensajeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mensaje")
 */
class MensajeController extends AbstractController
{
    /**
     * @Route("/", name="mensaje_index", methods={"GET"})
     */
    public function index(MensajeRepository $mensajeRepository): Response
    {

        return $this->render('mensaje/index.html.twig', [
            'mensajes' => $mensajeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="mensaje_new", methods={"GET","POST"})
     */
    public function new(Request $request, EstadoMensajeRepository $emr): Response
    {
        $mensaje = new Mensaje();
        $usr=$this->getUser();
        $mensaje->setEnvia($usr);
//        $mensaje->setEstado($emr->find(1));
        $form = $this->createForm(MensajeType::class, $mensaje);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mensaje);
            $entityManager->flush();
            $this->addFlash('success','Se ha enviado el mensaje correctamente');
            return $this->redirectToRoute('user_msjs');
        }

        return $this->render('mensaje/new.html.twig', [
            'mensaje' => $mensaje,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="mensaje_show", methods={"GET"})
     */
    public function show(Mensaje $mensaje): Response
    {
        $usr=$this->getUser();
       if($mensaje->getEnvia()->getId()== $usr->getId() || $mensaje->getRecibe()->getId() == $usr->getId() ){
           if($mensaje->getRecibe()->getId()== $usr->getId() && $mensaje->getLeido()===false){
                $mensaje->setLeido(true);
                $this->getDoctrine()->getManager()->persist($mensaje);
                $this->getDoctrine()->getManager()->flush();
           }
           return $this->render('mensaje/show.html.twig', [
               'mensaje' => $mensaje,
           ]);
       }
       $this->addFlash('error','Usted no tiene permitido acceder a ese mensaje');
       return $this->redirectToRoute('portada');
    }

    /**
     * @Route("/{id}/edit", name="mensaje_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Mensaje $mensaje): Response
    {

        $form = $this->createForm(MensajeType::class, $mensaje);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('mensaje_index');
        }

        return $this->render('mensaje/edit.html.twig', [
            'mensaje' => $mensaje,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="mensaje_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Mensaje $mensaje): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mensaje->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mensaje);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_msjs');
    }
}
