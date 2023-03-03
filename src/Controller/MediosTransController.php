<?php

namespace App\Controller;

use App\Entity\Basificacion;
use App\Entity\Lotjuridicas;
use App\Entity\MediosTrans;
use App\Entity\Tipomedio;
use App\Form\MediosTransType;
use App\Repository\MediosTransRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mediostrans")
 */
class MediosTransController extends AbstractController
{
    /**
     * @Route("/mar", name="medios_trans_mar", methods={"GET"})
     */
    public function indexMar(MediosTransRepository $mediosTransRepository): Response
    {
        return $this->render('medios_trans/index.html.twig', [
            'medios_trans' => $mediosTransRepository->findByRama(2),
        ]);
    }

    /**
     * @Route("/aut", name="medios_trans_aut", methods={"GET"})
     */
    public function indexAut(MediosTransRepository $mediosTransRepository): Response
    {
        return $this->render('medios_trans/index.html.twig', [
            'medios_trans' => $mediosTransRepository->findByRama(1),
        ]);
    }

    /**
     * @Route("/fc", name="medios_trans_fc", methods={"GET"})
     */
    public function indexFc(MediosTransRepository $mediosTransRepository): Response
    {
        return $this->render('medios_trans/index.html.twig', [
            'medios_trans' => $mediosTransRepository->findByRama(3),
        ]);
    }

    /**
     * @Route("/", name="medios_trans_index", methods={"GET"})
     */
    public function index(MediosTransRepository $repository, Request $request, PaginatorInterface $paginator): Response
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
        return $this->render('medios_trans/index.html.twig', [
            'pagination' => $pagination,
            'nombre'=>$nombre,
            'lot'=>$lot,
        ]);
    }

    /**
     * @Route("/new", name="medios_trans_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $mediosTran = new MediosTrans();
        $form = $this->createForm(MediosTransType::class, $mediosTran);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mediosTran);
            $entityManager->flush();

            return $this->redirectToRoute('medios_trans_index');
        }

        return $this->render('medios_trans/new.html.twig', [
            'medios_tran' => $mediosTran,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idbasif}/newmedio", name="medios_trans_newbyBasif", methods={"GET","POST"})
     */
    public function newbyBasif(Request $request, Basificacion $idbasif ): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $mun= $idbasif->getIdmun();
        if($mun->getProvinciaid()->getId() == $this->getUser()->getMunicipio()->getProvinciaid()->getId()){
            $mediosTran = new MediosTrans();
            $mediosTran->setBasificacionObj($idbasif);
            $mediosTran->setRama($idbasif->getIdlicencia()->getIdrama());
//        $serv=$idbasif->getIdlicencia()->getServicioamparado()->getDescServicioAmparado();

            $mediosTran->setServicio($this->DetServ($idbasif->getIdlicencia(),$mediosTran));

            $tipos = $entityManager->getRepository(Tipomedio::class)->findBy(['rama'=>$mediosTran->getRama()->getId()]);
            $form = $this->createForm(MediosTransType::class, $mediosTran,['tipos'=>$tipos]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $medio = $entityManager->getRepository(MediosTrans::class)->findBy(['nombre'=>$mediosTran->getNombre(),'rama'=>$mediosTran->getRama()->getId()]);
                if($medio==null){
                    $entityManager->persist($mediosTran);
                    $entityManager->flush();
                    $this->addFlash('success',"Se ha registrado el medio correctamente, el mismo se ha adicionado a la basificacion $idbasif");
                }
                else{
                    $this->addFlash('error'," Error. Ya existe un medio con esa matricula");
                    $dondeEstaba = $request->server->get('HTTP_REFERER');
                    if($dondeEstaba)
                        return new RedirectResponse($dondeEstaba, 302);
                }
                return $this->redirectToRoute('basificacion_show',['idlbasiam'=>$idbasif->getIdlbasiam()]);
            }

            return $this->render('medios_trans/new.html.twig', [
                'medios_tran' => $mediosTran,
                'form' => $form->createView(),
            ]);
        }
        else{
            $this->addFlash('error',"Solo los tramitadores pertenecientes al municipio $mun. Pueden Realizar esa Acción");
            return $this->redirectToRoute('basificacion_show',['idlbasiam'=>$idbasif->getIdlbasiam()]);
        }

    }

    /**
     * @Route("/{id}", name="medios_trans_show", methods={"GET"})
     */
    public function show(MediosTrans $mediosTran): Response
    {
        return $this->render('medios_trans/show.html.twig', [
            'medios_tran' => $mediosTran,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="medios_trans_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MediosTrans $mediosTran): Response
    {
        $tipos= $this->getDoctrine()->getRepository(Tipomedio::class)->findBy(['rama'=>$mediosTran->getRama()->getId()]);

        $form = $this->createForm(MediosTransType::class, $mediosTran,['tipos'=>$tipos]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('medios_trans_index');
        }

        return $this->render('medios_trans/edit.html.twig', [
            'medios_tran' => $mediosTran,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="medios_trans_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MediosTrans $mediosTran): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mediosTran->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mediosTran);
            $entityManager->flush();
        }

        return $this->redirectToRoute('medios_trans_index');
    }
    private function DetServ(Lotjuridicas $lot, MediosTrans $medio){
        $tipo= $lot->getIdtipo();
//        $servicio = $lot->getIdservicio();
        $servAmp = $lot->getServicioamparado();
        $rama= $lot->getIdrama();
//        $ext = $lot->getIdextension();
        $limitacion = $lot->getLimitacion();
        $aseg = $medio->getAseguramiento() ? "Aseguramiento" : "";
        $serv= "$tipo de $servAmp. $rama. $aseg";
        if($tipo->getId()==2){
            $serv = "$servAmp .Limitado a $limitacion. $rama. $aseg";
        }
        return $serv;
    }

}
