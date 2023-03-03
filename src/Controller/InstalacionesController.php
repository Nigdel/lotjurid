<?php

namespace App\Controller;

use App\Entity\Compestab;
use App\Entity\Instalaciones;
use App\Entity\Lotjuridicas;
use App\Entity\Municipios;
use App\Entity\TipoServAuxCon;
use App\Form\InstalacionesType;
use App\Repository\InstalacionesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/instalaciones")
 */
class InstalacionesController extends AbstractController
{
    /**
     * @Route("/", name="instalaciones_index", methods={"GET"})
     */
    public function index(InstalacionesRepository $repository,Request $request, PaginatorInterface $paginator): Response
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
                $queryBuilder = $repository->getWithSearchQueryBuilder();
        }
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('instalaciones/index.html.twig', [
            'pagination' => $pagination,
            'nombre'=>$nombre,
            'lot'=>$lot,
        ]);
    }

    /**
     * @Route("/new", name="instalaciones_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $instalacione = new Instalaciones();

        $form = $this->createForm(InstalacionesType::class, $instalacione);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($instalacione);
            $entityManager->flush();

            return $this->redirectToRoute('instalaciones_index');
        }

        return $this->render('instalaciones/new.html.twig', [
            'instalacione' => $instalacione,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/newbylot/{lotjuridicas}", name="instalaciones_newbylot", methods={"GET","POST"})
     */
    public function newbylot(Request $request, Lotjuridicas $lotjuridicas): Response
    {
        if ($lotjuridicas->getIdestado()->getId()==5){
            $instalacione = new Instalaciones();
            $instalacione->setLot($lotjuridicas);
            $instalacione->setMunicipio($this->getUser()->getMunicipio());
            $mcpios=$this->getDoctrine()->getManager()->getRepository(Municipios::class)->findBy(['provinciaid'=>$this->getUser()->getMunicipio()->getProvinciaId()->getId()]);
            $aseguramiento = false;
            $servicios = null;
            if($lotjuridicas->getIdservicio()->getId()!= 3){
                $aseguramiento = true;
                $servicios = $this->getDoctrine()->getRepository(TipoServAuxCon::class)->findBy(['rama'=>$lotjuridicas->getIdrama()->getId()]);

            }
            $form = $this->createForm(InstalacionesType::class, $instalacione,['municipios'=>$mcpios, 'aseguramiento'=>$aseguramiento, 'servicios'=>$servicios]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($instalacione);
                $entityManager->flush();

                return $this->redirectToRoute('instalaciones_index');
            }
            return $this->render('instalaciones/newbylot.html.twig', [
                'instalacione' => $instalacione,
                'form' => $form->createView(),
            ]);
        }
        else{
            $this->addFlash('error', "La lot $lotjuridicas debe encontrarse Vigente para realizar ese trámite!");
            return $this->redirectToRoute('lotjuridicas_show',['id'=>$lotjuridicas->getId()]);
        }

    }

    /**
     * @Route("/{id}", name="instalaciones_show", methods={"GET"})
     */
    public function show(Instalaciones $instalacione): Response
    {
        $servicios = $this->getDoctrine()->getRepository(TipoServAuxCon::class)->findBy(['rama'=>$instalacione->getLot()->getIdrama()->getId()]);
        $servs = $instalacione->getServicios1();


        return $this->render('instalaciones/show.html.twig', [
            'instalacione' => $instalacione,
            'comp'=> $instalacione->compActual(),
            'servicios'=>$servicios,
            'servs'=>$servs,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="instalaciones_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Instalaciones $instalacione): Response
    {
        $servicios = $this->getDoctrine()->getRepository(TipoServAuxCon::class)->findBy(['rama'=>$instalacione->getLot()->getIdrama()->getId()]);
        $form = $this->createForm(InstalacionesType::class, $instalacione,['servicios'=>$servicios]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('instalaciones_index');
        }

        return $this->render('instalaciones/edit.html.twig', [
            'instalacione' => $instalacione,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="instalaciones_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Instalaciones $instalacione): Response
    {
        if ($this->isCsrfTokenValid('delete'.$instalacione->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($instalacione);
            $entityManager->flush();
        }

        return $this->redirectToRoute('instalaciones_index');
    }





}
