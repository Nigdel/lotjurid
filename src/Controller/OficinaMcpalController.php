<?php

namespace App\Controller;

use App\Entity\DireccionProvincial;
use App\Entity\Municipios;
use App\Entity\OficinaMcpal;
use App\Entity\User;
use App\Form\OficinaMcpalType;
use App\Repository\OficinaMcpalRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * @Route("/oficina/mcpal")
 */
class OficinaMcpalController extends AbstractController
{
    /**
     * @Route("/", name="oficina_mcpal_index", methods={"GET"})
     */
    public function index(OficinaMcpalRepository $oficinaMcpalRepository): Response
    {
        $usuario = $this->getUser();
//        dump($usuario);
        $roles= $usuario->getRoles();
        $ofic= $usuario->getOficinaMcpal();
        if(!array_search("ROLE_ADMIN",$roles)&&!array_search("ROLE_LOTPROV",$roles)&&$ofic!==null){
            return $this->redirectToRoute("oficina_mcpal_show",['id'=>$ofic->getId()]);
        }
        return $this->render('oficina_mcpal/index.html.twig', [
            'oficina_mcpals' => $oficinaMcpalRepository->findAll(),
        ]);
    }
    /**
     * @Route("/newbydp/{dp}", name="oficina_mcpal_newbydp", methods={"GET","POST"})
     */
    public function newbydp(Request $request, DireccionProvincial $dp): Response
    {
        $oficinaMcpal = new OficinaMcpal();
        $oficinaMcpal->setDireccionProvincial($dp);
        $mcpios=$this->getDoctrine()->getManager()->getRepository(Municipios::class)->findBy(['provinciaid'=>$dp->getProvincia()->getId()]);
        $form = $this->createForm(OficinaMcpalType::class, $oficinaMcpal,['municipios'=>$mcpios]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            try{
                $data = $form->getData();
                $oficinaMcpal->addFuncionario($data->getDirector());
                $oficinaMcpal->setEmail($data->getDirector());
                $entityManager->persist($oficinaMcpal);
                $entityManager->flush();
            }
            catch (UniqueConstraintViolationException $exception ){
                $this->addFlash('error',"Verifique que el usuario seleccionado como jefe de oficina no se haya declarado como tal en otra OM");
            }

            return $this->redirectToRoute('oficina_mcpal_index');
        }

        return $this->render('oficina_mcpal/new.html.twig', [
            'oficina_mcpal' => $oficinaMcpal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="oficina_mcpal_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $oficinaMcpal = new OficinaMcpal();
        $form = $this->createForm(OficinaMcpalType::class, $oficinaMcpal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            try{
                $entityManager->persist($oficinaMcpal);
                $entityManager->flush();
            }
            catch (UniqueConstraintViolationException $exception ){
                $this->addFlash('error',"Verifique que el usuario seleccionado como jefe de oficina no se haya declarado como tal en otra OM");
            }

            return $this->redirectToRoute('oficina_mcpal_index');
        }

        return $this->render('oficina_mcpal/new.html.twig', [
            'oficina_mcpal' => $oficinaMcpal,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="oficina_mcpal_show", methods={"GET"})
     */
    public function show(OficinaMcpal $oficinaMcpal): Response
    {
//        dump($oficinaMcpal->getDireccionProvincial());
        $funcionarios = $this->getDoctrine()->getManager()->getRepository("App\Entity\User")
            ->findFuncToOM($oficinaMcpal);
        return $this->render('oficina_mcpal/show.html.twig', [
            'oficina_mcpal' => $oficinaMcpal,'funcionarios'=>$funcionarios
        ]);
    }

    /**
     * @Route("/{id}/edit", name="oficina_mcpal_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, OficinaMcpal $oficinaMcpal): Response
    {

        $form = $this->createForm(OficinaMcpalType::class, $oficinaMcpal,['funcionarios'=>$oficinaMcpal->getFuncionarios()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('oficina_mcpal_index');
        }

        return $this->render('oficina_mcpal/edit.html.twig', [
            'oficina_mcpal' => $oficinaMcpal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="oficina_mcpal_delete", methods={"DELETE"})
     */
    public function delete(Request $request, OficinaMcpal $oficinaMcpal): Response
    {
        if ($this->isCsrfTokenValid('delete'.$oficinaMcpal->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($oficinaMcpal);
            $entityManager->flush();
        }

        return $this->redirectToRoute('oficina_mcpal_index');
    }

    /**
     * @Route("/{id}/addFunc/{idfunc}", name="oficina_mcpal_addFunc", methods={"GET","POST"})
     */
    public function addFuncionario(OficinaMcpal $oficinaMcpal,$idfunc ): Response{

        $func = $this->getDoctrine()->getManager()->getRepository("App\Entity\User")->find($idfunc);
        $func->setOficinaMcpal($oficinaMcpal);
        $this->getDoctrine()->getManager()->persist($func);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success',"El funcionario se ha añadido con exito");
        return $this->redirectToRoute("oficina_mcpal_show",['id'=>$oficinaMcpal->getId()]);
    }

    /**
     * @Route("/{id}/removeFunc/{idfunc}", name="oficina_mcpal_removeFunc")
     */
    public function removeFunc(OficinaMcpal $oficinaMcpal,User $idfunc){
        $idfunc->setOficinaMcpal(null);
        $this->getDoctrine()->getManager()->persist($idfunc);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute("oficina_mcpal_show",['id'=>$oficinaMcpal->getId()]);
    }

}
