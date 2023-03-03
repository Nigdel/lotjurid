<?php

namespace App\Controller;

use App\Entity\Basificacion;
use App\Entity\CausaCancelacionComp;
use App\Entity\Lotjuridicas;
use App\Entity\MediosTrans;
use App\Entity\Municipios;
use App\Entity\NombEstComp;
use App\Form\BasificacionType;
use App\Repository\BasificacionRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/basificacion")
 */
class BasificacionController extends AbstractController
{
    /**
     * @Route("/", name="basificacion_index", methods={"GET"})
     */
    public function index( BasificacionRepository $repository, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = null;
        $nombre=null;
        $lot=null;
        $search = $request->query->get('nombre');
        if ($search) {
            $queryBuilder = $repository->searchNombre($search);
            $nombre= $search;
        }else{
            $search = $request->query->get('lot');
            if ($search){
                $queryBuilder = $repository->searchLot($search);
                $lot=$search;
            }
            else
                $queryBuilder = $repository->getWithSearchQueryBuilder($this->getUser());
        }
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            30/*limit per page*/
        );

        return $this->render('basificacion/index.html.twig', [
            'pagination' => $pagination,
            'nombre'=>$nombre,
            'lot'=>$lot,
        ]);
    }

    /**
     * @Route("/new", name="basificacion_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $basificacion = new Basificacion();
        $usr= $this->getUser();
        $roles= $usr->getRoles();
        if(array_search("ROLE_ADMIN",$roles))
        {
            $form = $this->createForm(BasificacionType::class, $basificacion);
        }
        else{
            $basificacion->setIdmun($usr->getMunicipio());
            $mcpios=$this->getDoctrine()->getManager()->getRepository(Municipios::class)->findBy(['provinciaid'=>$usr->getMunicipio()->getProvinciaId()->getId()]);
            $form = $this->createForm(BasificacionType::class, $basificacion,['municipios'=> $mcpios]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($basificacion);
            $entityManager->flush();

            return $this->redirectToRoute('basificacion_index');
        }

        return $this->render('basificacion/new.html.twig', [
            'basificacion' => $basificacion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/newbylot/{lotjuridicas}", name="basificacion_newbylot", methods={"GET","POST"})
     */
    public function newbyLot(Request $request, Lotjuridicas $lotjuridicas): Response
    {
        $basificacion = new Basificacion();
        $basificacion->setIdlicencia($lotjuridicas);

        $usr= $this->getUser();
        $basificacion->setIdmun($usr->getMunicipio());
        $roles= $usr->getRoles();
        if(array_search("ROLE_ADMIN",$roles))
        {
            $form = $this->createForm(BasificacionType::class, $basificacion);
        }
        else{
            $basificacion->setIdmun($usr->getMunicipio());
            $mcpios=$this->getDoctrine()->getManager()->getRepository(Municipios::class)->findBy(['provinciaid'=>$usr->getMunicipio()->getProvinciaId()->getId()]);
            $form = $this->createForm(BasificacionType::class, $basificacion,['municipios'=> $mcpios]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($basificacion);
            $entityManager->flush();

            return $this->redirectToRoute('basificacion_index');
        }

        return $this->render('basificacion/newbylot.html.twig', [
            'basificacion' => $basificacion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idlbasiam}", name="basificacion_show", methods={"GET"})
     */
    public function show(Basificacion $basificacion): Response
    {
        return $this->render('basificacion/show.html.twig', [
            'basificacion' => $basificacion,
        ]);
    }

    /**
     * @Route("/{idlbasiam}/permutar", name="basificacion_permutar" )
     */
    public function permutar(Basificacion $basificacion, Request $request): Response{
        $em= $this->getDoctrine()->getManager();
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('medios', EntityType::class,[
                'class' => MediosTrans::class,
                'query_builder' => function (EntityRepository $er) use ($basificacion) {
                    return $er->createQueryBuilder('m')
                              ->join('m.basificacionObj','b')
                              ->andWhere('b.idlbasiam = :val')
                              ->setParameter('val', $basificacion->getIdlbasiam());
                },
                 'choice_label' => 'nombre',
                 'multiple' => true,
            ])
            ->add('basificacion', EntityType::class,[
                'class'=> Basificacion::class,
                'attr'=>['class'=>'mi-selector'],
            ])
            ->add('Mover', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() ){
            $medios = $form["medios"]->getData();
            dump($medios);
            $nuevaBase = $form["basificacion"]->getData();
            foreach ($medios as $medio){
                $medio->setBasificacionObj($nuevaBase);               
                $comprobante=  $medio->getComprobanteActivo();
                if($comprobante){
                    $estado= $comprobante->getEstadoComp()->getId();
                    if($estado == 4 || $estado == 6 || $estado == 7  ){                    
                        // $comprobante->setFcancel(date('d/m/Y'));
                        $comprobante->setFcancel(\DateTime::createFromFormat('d/m/Y',date('d/m/Y'))); 
                        $comprobante->setCCancel($em->getRepository(CausaCancelacionComp::class)->find(1));
                        $comprobante->setEstadoComp($em->getRepository(NombEstComp::class)->find(8));
                        $em->persist($comprobante);   
                        $this->addFlash('success', "El comp $comprobante ahora se encuentra Cancelado! ");              
                    }  
                }
                $em->persist($medio);
                              
            } 
            $em->flush();
            $this->addFlash('success', "Se han realizado los cambios satisfactoriamente");  
        //    return $this->redirectToRoute("basificacion_show", ['idlbasiam' => $nuevaBase->getidlbasiam()]);
        }
         
        return $this->render('basificacion/permuta.html.twig',[
            'basificacion'=>$basificacion,
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/{idlbasiam}/edit", name="basificacion_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Basificacion $basificacion): Response
    {
        $form = $this->createForm(BasificacionType::class, $basificacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('basificacion_index');
        }

        return $this->render('basificacion/edit.html.twig', [
            'basificacion' => $basificacion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idlbasiam}", name="basificacion_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Basificacion $basificacion): Response
    {
        if ($this->isCsrfTokenValid('delete'.$basificacion->getIdlbasiam(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($basificacion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('basificacion_index');
    }

}
