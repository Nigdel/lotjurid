<?php

namespace App\Controller;

use App\Entity\DireccionProvincial;
use App\Entity\OficinaMcpal;
use App\Entity\User;
use App\Form\DireccionProvincialType;
use App\Repository\DireccionNacionalRepository;
use App\Repository\DireccionProvincialRepository;
use App\Repository\OficinaMcpalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/direccion/provincial")
 */
class DireccionProvincialController extends AbstractController
{
    /**
     * @Route("/", name="direccion_provincial_index", methods={"GET"})
     */
    public function index(DireccionProvincialRepository $direccionProvincialRepository): Response
    {
        $usuario = $this->getUser();
        $roles= $usuario->getRoles();
        $ofic= $usuario->getDireccionProvincial();
        if(!array_search("ROLE_ADMIN",$roles)&&!array_search("ROLE_LOTNAC",$roles)&&$ofic!==null){
            return $this->redirectToRoute("direccion_provincial_show",['id'=>$ofic->getId()]);
        }
        $ofmcpal= $usuario->getOficinaMcpal();
        if($ofmcpal!==null){
            return $this->redirectToRoute("direccion_provincial_show",['id'=>$ofmcpal->getDireccionProvincial()->getId()]);
        }
        $dp = $direccionProvincialRepository->findOneBy(['provincia'=>$usuario->getMunicipio()->getProvinciaid()->getId()]);
        if($dp!==null){
            return $this->redirectToRoute("direccion_provincial_show",['id'=>$dp->getId()]);
        }

        return $this->render('direccion_provincial/index.html.twig', [
            'direccion_provincials' => $direccionProvincialRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="direccion_provincial_new", methods={"GET","POST"})
     */
    public function new(Request $request, DireccionNacionalRepository $dn): Response
    {
        $direccionProvincial = new DireccionProvincial();
        $direccionProvincial->setDireccionNacional($dn->find(1));
        $form = $this->createForm(DireccionProvincialType::class, $direccionProvincial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($direccionProvincial);
            $entityManager->flush();

            return $this->redirectToRoute('direccion_provincial_index');
        }

        return $this->render('direccion_provincial/new.html.twig', [
            'direccion_provincial' => $direccionProvincial,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="direccion_provincial_show", methods={"GET"})
     */
    public function show(DireccionProvincial $direccionProvincial): Response
    {
        $funcionarios = $this->getDoctrine()->getManager()->getRepository("App\Entity\User")
            ->findFuncToDP($direccionProvincial);
        return $this->render('direccion_provincial/show.html.twig', [
            'direccion_provincial' => $direccionProvincial,'funcionarios'=>$funcionarios
        ]);
    }

    /**
     * @Route("/{id}/edit", name="direccion_provincial_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, DireccionProvincial $direccionProvincial): Response
    {
        $funcionarios = $direccionProvincial->getFuncionarios();

        $form = $this->createForm(DireccionProvincialType::class, $direccionProvincial,['funcionarios'=> $funcionarios]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success','Se ha actualizado la informacion correctamente');
            return $this->redirectToRoute('direccion_provincial_index');
        }


        return $this->render('direccion_provincial/edit.html.twig', [
            'direccion_provincial' => $direccionProvincial,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="direccion_provincial_delete", methods={"DELETE"})
     */
    public function delete(Request $request, DireccionProvincial $direccionProvincial): Response
    {
        if ($this->isCsrfTokenValid('delete'.$direccionProvincial->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($direccionProvincial);
            $entityManager->flush();
        }

        return $this->redirectToRoute('direccion_provincial_index');
    }
    /**
     * @Route("/{id}/addFunc/{idfunc}", name="direccion_provincial_addFunc", methods={"GET","POST"})
     */
    public function addFuncionario(DireccionProvincial $direccionProvincial,$idfunc ): Response{

        $func = $this->getDoctrine()->getManager()->getRepository("App\Entity\User")->find($idfunc);
        $func->setDireccionProvincial($direccionProvincial);
        $this->getDoctrine()->getManager()->persist($func);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute("direccion_provincial_show",['id'=>$direccionProvincial->getId()]);
    }

    /**
     * @Route("/{id}/removeFunc/{idfunc}", name="direccion_provincial_removeFunc")
     */
    public function removeFunc(DireccionProvincial $direccionProvincial,User $idfunc){
        $idfunc->setDireccionProvincial(null);
        $this->getDoctrine()->getManager()->persist($idfunc);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute("direccion_provincial_show",['id'=>$direccionProvincial->getId()]);
    }

    /**
     * @Route("/{id}/addOm/{idom}", name="direccion_provincial_addOM")
     */
    public function addOM(DireccionProvincial $direccionProvincial,OficinaMcpal $oficinaMcpal){
        $oficinaMcpal->setDireccionProvincial($direccionProvincial);
        $this->getDoctrine()->getManager()->persist($oficinaMcpal);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute("direccion_provincial_show",['id'=>$direccionProvincial->getId()]);
    }
    /**
     * @Route("/{id}/regNewOm",name="direccion_provincial_regNewOm")
     */
    public function regNewOm(Request $request,DireccionProvincial $direccionProvincial,OficinaMcpalRepository $mcpalRepository){
        $om = new OficinaMcpal();
        $om->setDireccionProvincial($direccionProvincial);
        $form = $this->createFormBuilder($om)
            ->add('direccion')
            ->add('telefono')
            ->add('email')
            ->add('director')
            ->add('municipio')
            ->add('Crear',SubmitType::class,[
                    'attr'=>[
                            'class'=>'btn btn-success'
                            ]
                    ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()){
            if($form->isValid()){

                $data = $form->getData();
                dump($data);
                dump($om);

                $em = $this->getDoctrine()->getManager();
                $em->persist($om);
                $em->flush();
                return $this->redirect($this->generateUrl('direccion_provincial_show',['id'=>$direccionProvincial->getId()]));
            }
        }
        return $this->render('oficina_mcpal/new.html.twig', [
            'form'=> $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/ingresosxdiaxmcpio",name="direccion_provincial_ixdxm")
     * @param DireccionProvincial $dp
     * @return Response
     */
    public function ingresosXdiaXmcpio(DireccionProvincial $dp){




        return new Response('Ayuda');
    }

    /**
     * @Route("/{dp}/ingresos", name="direccion_provincial_ingresos"    )
     */
    public function ingresos(DireccionProvincial $dp){
        $user = $this->getUser();
        $prov= $user->getMunicipio()->getProvinciaid()->getId();
        $db = $this->getDoctrine()->getConnection();

        $sql = "SELECT sum(`importe`) as 'importe',`concepto`,`provid`, count(*) as 'cant' FROM `ingresoshoygral` where `provid`=:prov GROUP by `concepto` ";
        $stmt = $db->prepare($sql);
        $params = array('prov'=>$prov);
        $stmt->execute($params);
        $ingresos=$stmt->fetchAll();


//        $sql = "SELECT sum(`importe`) as 'importe',`concepto`,`provid`, count(*) as 'cant' FROM `ingresoshoygral` where `provid`=:prov GROUP by `concepto` ";
        $sql ="SELECT 
sum(if(DAYOFMONTH(`FechaEntrega`)=1,`importe`,0) ) as '1' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=2,`importe`,0) ) as '2' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=3,`importe`,0) ) as '3' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=4,`importe`,0) ) as '4' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=5,`importe`,0) ) as '5' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=6,`importe`,0) ) as '6' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=7,`importe`,0) ) as '7',
sum(if(DAYOFMONTH(`FechaEntrega`)=8,`importe`,0) ) as '8',
sum(if(DAYOFMONTH(`FechaEntrega`)=9,`importe`,0) ) as '9',
sum(if(DAYOFMONTH(`FechaEntrega`)=10,`importe`,0) ) as '10' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=12,`importe`,0) ) as '12' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=13,`importe`,0) ) as '13' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=14,`importe`,0) ) as '14' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=15,`importe`,0) ) as '15' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=16,`importe`,0) ) as '16' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=17,`importe`,0) ) as '17',
sum(if(DAYOFMONTH(`FechaEntrega`)=18,`importe`,0) ) as '18',
sum(if(DAYOFMONTH(`FechaEntrega`)=19,`importe`,0) ) as '19',
sum(if(DAYOFMONTH(`FechaEntrega`)=20,`importe`,0) ) as '20',
sum(if(DAYOFMONTH(`FechaEntrega`)=21,`importe`,0) ) as '21' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=22,`importe`,0) ) as '22' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=23,`importe`,0) ) as '23' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=24,`importe`,0) ) as '24' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=25,`importe`,0) ) as '25' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=26,`importe`,0) ) as '26' ,
sum(if(DAYOFMONTH(`FechaEntrega`)=27,`importe`,0) ) as '27',
sum(if(DAYOFMONTH(`FechaEntrega`)=28,`importe`,0) ) as '28',
sum(if(DAYOFMONTH(`FechaEntrega`)=29,`importe`,0) ) as '29',
sum(if(DAYOFMONTH(`FechaEntrega`)=30,`importe`,0) ) as '30',
sum(if(DAYOFMONTH(`FechaEntrega`)=31,`importe`,0) ) as '31'
from 
lotsentregestemes";



        $stmt = $db->prepare($sql);
        $params = array('prov'=>$prov);
        $stmt->execute($params);
        $ingresosmes=$stmt->fetchAll();

//        date('')
        return $this->render("direccion_provincial/ingresos.html.twig",[
            'ingresos'=>$ingresos,
            'ingresosmes'=>$ingresosmes,
        ]);
    }

}
