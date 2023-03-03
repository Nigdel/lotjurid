<?php

namespace App\Controller;

use App\Entity\CausaCancelacionComp;
use App\Entity\CausaSuspensionComp;
use App\Entity\Compestab;
use App\Entity\Instalaciones;
use App\Entity\NombEstComp;
//use App\Form\Compestab1Type;
use App\Entity\TipoServAuxCon;
use App\Form\CompestabType;
use App\Repository\CompestabRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use phpDocumentor\Reflection\Types\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/compestab")
 */
class CompestabController extends AbstractController
{
    /**
     * @Route("/", name="compestab_index", methods={"GET"})
     */
    public function index(CompestabRepository $compestabRepository,Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = null;
        $insta= null;
        $folio=null;
        $lot= null;
        $search = $request->query->get('insta');
        if ($search) {
            $queryBuilder = $compestabRepository->searchInsta($search);
            $insta= $search;
        } else{
            $search = $request->query->get('lot');
            if($search){
                $queryBuilder = $compestabRepository->searchLot($search);
                $lot= $search;
            }
            else{
                $search = $request->query->get('folio');
                if ($search) {
                    $queryBuilder = $compestabRepository->searchFolio($search);
                    $folio= $search;
                }else{
                    $queryBuilder = $compestabRepository->getWithSearchQueryBuilder();
                }
            }
        }
        //dump($request);
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            20/*limit per page*/
        );
        return $this->render('compestab/index.html.twig', [
            'compestabs' => $compestabRepository->findAll(),
            'pagination' => $pagination,
            'insta'=>$insta,
            'folio'=>$folio,
            'lot'=>$lot,
        ]);
    }

    /**
     * @Route("/new", name="compestab_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $compestab = new Compestab();
        $form = $this->createForm(CompestabType::class, $compestab);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $compestab->setImporte(MainController::Tarifa($entityManager,'Comprobantes y Duplicados',$compestab->getInstalacion()->getLot()->getIdextension())->getValor());
            $entityManager->persist($compestab);
            $entityManager->flush();

            return $this->redirectToRoute('compestab_index');
        }
        return $this->render('compestab/new.html.twig', [
            'compestab' => $compestab,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/newbyinst/{inst}", name="compestab_newbyinst", methods={"GET","POST"})
     */
    public function newbyinst(Request $request, Instalaciones $inst): Response
    {
        $estlot= strtolower($inst->getLot()->getIdestado()->getNombreEstado());
        if($estlot=='vigente'){
            $entityManager = $this->getDoctrine()->getManager();
            $compestab = new Compestab();
            $servicios = $this->getDoctrine()->getRepository(TipoServAuxCon::class)->findBy(['rama'=>$inst->getLot()->getIdrama()->getId()]);
            $compestab->setInstalacion($inst);
            $compestab->setExtensionID($inst->getLot()->getIdextension());
            $compestab->setEstadoComp($entityManager->getRepository(NombEstComp::class)->find(1));
            $compestab->setImporte(MainController::Tarifa($entityManager,'Comprobantes y Duplicados',$compestab->getInstalacion()->getLot()->getIdextension())->getValor());
            $form = $this->createForm(CompestabType::class, $compestab);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $compestab->setEstadoComp($entityManager->getRepository(NombEstComp::class)->find(2));
                $nuevafecha = new \DateTime($compestab->getFemitido()->format('Y-m-d'));
                $compestab->setFvencimiento( $nuevafecha->add(new \DateInterval('P3Y')));
                $entityManager->persist($compestab);
                $entityManager->flush();
                $this->addFlash('success', "El comp $compestab ahora se encuentra Pendiente de Impresion! ");
                return $this->redirectToRoute('compestab_index');
            }
            return $this->render('compestab/newbyinst.html.twig', [
                'compestab' => $compestab,
                'form' => $form->createView(),
                'servicios'=>$servicios,
            ]);
        }
        else{

            $this->addFlash('error',"La lot debe estar Vigente para realizar esa acción, la misma se encuentra $estlot");
            return $this->redirectToRoute('lotjuridicas_show',['id'=>$inst->getLot()->getId()]);
        }

    }

    /**
     * @Route("/ptesimpres",name="compestab_ptesimpress")
     * @param CompestabRepository $comprobanteRepository
     * @return Response
     */
    public function ptesImpress(CompestabRepository $comprobanteRepository,Request $request){
        if($request->getMethod()=="POST"){
            $folio= $request->get('foliovalor');
            $comp= $request->get('comp');
            return $this->redirectToRoute("compestab_imprimir",['id'=>$comp,'folio'=>$folio]);
        }
        return $this->render('compestab/tramiteCompbase.html.twig',[
            'tramitecomp'=>"Pendientes de Impresion",
            'comps'=>$comprobanteRepository->findBy(['estadoComp'=>2])
        ]);
    }

    /**
     * @Route("/ptesentrega",name="compestab_ptesentrega")
     * @param CompestabRepository $comprobanteRepository
     * @return Response
     */
    public function ptesEntrega(CompestabRepository $comprobanteRepository){
        return $this->render('compestab/tramiteCompbase.html.twig',[
            'tramitecomp'=>"Pendientes de Entrega",
            'comps'=>$comprobanteRepository->findBy(['estadoComp'=>3])
        ]);
    }

    /**
     * @Route("/{id}", name="compestab_show", methods={"GET"})
     */
    public function show(Compestab $compestab): Response
    {
        $servicios = $this->getDoctrine()->getRepository(TipoServAuxCon::class)->findBy(['rama'=>$compestab->getInstalacion()->getLot()->getIdrama()->getId()]);
        return $this->render('compestab/show.html.twig', [
            'compestab' => $compestab,
            'servicios'=>$servicios,
        ]);
    }

    /**
     * @Route("/{id}/imprimir/{folio}", name="compestab_imprimir")
     */
    public function imprimir(Request $request, Compestab $comprobante,string $folio){
        $em = $this->getDoctrine()->getManager();
        if($folio !== null && $comprobante->getEstadoComp()->getId()==2){
            //revisr que el folio no se haya asignado a otro comp
            $existe = $em->getRepository(Compestab::class)->findOneBy(['folio'=>$folio]);
            if($existe===null || $folio == $comprobante->getFolio()){
                $comprobante->setFolio($folio);
                $comprobante->setFimpreso(new \DateTime('today'));
                $pautoafirmar=null;
               if($this->getUser()->getOficinaMcpal()!==null)
                  $pautoafirmar= $this->getUser()->getOficinaMcpal()->getFirmacomp();
                elseif ($this->getUser()->getDireccionProvincial()!==null){
                    $pautoafirmar=$this->getUser()->getDireccionProvincial()->getFirmacomp();
                }
                if($pautoafirmar!==null && $pautoafirmar->getNombreApellidos()!==null && $pautoafirmar->getCargo()!==null){
                    $comprobante->setFirma($pautoafirmar->getNombreApellidos());
                    $comprobante->setFirmacargo($pautoafirmar->getCargo());
                }
                else{
                    $this->addFlash('error',"No se puede determinar el personal autorizado a firmar el comprobante o sus datos estan incompletos");
                    return $this->redirectToRoute('compestab_index');
                }
                $em->persist($comprobante);
                $em->flush();

                $comp = $comprobante;
                // Configure Dompdf according to your needs
                $pdfOptions = new Options();
                $pdfOptions->set('defaultFont', 'Arial');

                // Instantiate Dompdf with our options
                $dompdf = new Dompdf($pdfOptions);

                // Retrieve the HTML generated in our twig file
                $html = $this->renderView('compestab/comppdf.html.twig', [
                    'title' => "comp de Instalacion $comp",'comp'=>$comp,'folio'=>$folio
                ]);
                // Load HTML to Dompdf
                $dompdf->loadHtml($html);
                $filename=$comp->__toString();
                // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
//                $customPaper= array(0,0,612,396);
//                $dompdf->setPaper($customPaper,'portrait');
                $dompdf->setPaper('letter');

                // Render the HTML as PDF
                $dompdf->render();

                // Output the generated PDF to Browser (force download)
                $dompdf->stream($filename, [
                    "Attachment" => false
                ]);
            }
            else{
                $this->addFlash('error',"No se puede asignar el mismo folio a mas de un comprobante");
                return $this->redirectToRoute('compestab_index');
            }
        }
        else
        {
            $this->addFlash('error', "El comp $comprobante no tiene un folio v&aacute;lido asignado o no se ha imprimido aun");
            return $this->redirectToRoute('compestab_index');
        }
    }

    /**
     * @Route("/{id}/marcarcomoimpreso", name="compestab_marcarcomoimpreso")
     */
    public function marcarComoImpreso(Compestab $comprobante,Request $request){
        $em= $this->getDoctrine()->getManager();
        if($comprobante->getEstadoComp()->getId()==2 and $comprobante->getFolio()!= null){
            $comprobante->setEstadoComp($em->getRepository(NombEstComp::class)->find(3));
            $em->persist($comprobante);
            $em->flush();
            $this->addFlash('success', "El comprobante $comprobante ahora se encuentra Pendiente de Entrega! ");
        }
        else{
            $this->addFlash('error', "El comprobante $comprobante no tiene un folio v&aacute;lido asignado o no se encuentra en el estado correcto para esa operacion");
            $dondeEstaba = $request->server->get('HTTP_REFERER');
            return new RedirectResponse($dondeEstaba, 302);
        }
        return $this->redirectToRoute('compestab_ptesentrega');
    }

    /**
     * @Route("/retirados",name="compestab_retirados")
     * @param CompestabRepository $comprobanteRepository
     * @return Response
     */
    public function ocupados(CompestabRepository $comprobanteRepository){
        return $this->render('comprobante/tramiteCompbase.html.twig',[
            'tramitecomp'=>"Retirados",
            'comps'=>$comprobanteRepository->findBy(['estadoComp'=>6])
        ]);
    }

    /**
     * @Route("/{id}/finsusp",name="compestab_finsuspension")
     * @throws \Exception
     */
    public function finsuspension(Request $request, Compestab $comprobante){
        $em= $this->getDoctrine()->getManager();
        $form = $this->createForm( CompestabType::class, $comprobante);
        $form->handleRequest($request);
        if($request->getMethod()=="POST"){
            $estado= $comprobante->getEstadoComp()->getId();
            if($estado ==6 ){
                $fecha= $request->get('fechasusp');
                $reponer= $request->get('reponerT');
                $date1 = $comprobante->getFinicioSusp();
                $date2= \DateTime::createFromFormat('d/m/Y',$fecha);
                if ($date2 && $date2 > $date1 ){
                    $comprobante->setFfinSusp($date2);
                    $comprobante->setEstadoComp($em->getRepository(NombEstComp::class)->find(4));
                    if($reponer==='on'){
                        $diff = $date1->diff($date2)->days;
                        $venc= $comprobante->getFvencimiento();
                        $venc->add(new \DateInterval('P'.$diff.'D'));
                        $comprobante->setFvencimiento($venc);
                        $this->addFlash('success', "El comp $comprobante ahora se encuentra Vigente. Se ha extendido su fecha de vencimiento en $diff dias.");
                    }
                    else{
                        $this->addFlash('success', "El comp $comprobante ahora se encuentra Vigente! ");
                    }
                    $em->persist($comprobante);
                    $em->flush();
                    return $this->redirectToRoute('compestab_index');
                }else{
                    $this->addFlash('error', "El comp $comprobante no puede ser Rehabilitado. La fecha de incio de suspension no puede ser mayor a la de fin de suspension");
                }
            }
            else{
                $this->addFlash('error', "El comp $comprobante no puede ser Rehabilitado. El mismo no está Suspendido!");
            }
        }
        return $this->render('compestab/finsuspension.html.twig',[
            'compestab'=>$comprobante,
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/entregar", name="compestab_entregar")
     */
    public function entregar(Request $request,Compestab $comprobante):Response{
        $em= $this->getDoctrine()->getManager();
        $form = $this->createForm(CompestabType::class, $comprobante);
        $servicios = $this->getDoctrine()->getRepository(TipoServAuxCon::class)->findBy(['rama'=>$comprobante->getInstalacion()->getLot()->getIdrama()->getId()]);
        $form->handleRequest($request);
        if($request->getMethod()=="POST"){
            if($comprobante->getEstadoComp()->getId()==3){
                $comprobante->setFentrega(\DateTime::createFromFormat('d/m/Y',$request->get("fechaentrega","today")));
                $comprobante->setEstadoComp($em->getRepository(NombEstComp::class)->find(4));
                $comprobante->setImporte(MainController::Tarifa($em,'Comprobantes y Duplicados',$comprobante->getInstalacion()->getLot()->getIdextension())->getValor());
                $em->persist($comprobante);
                $em->flush();
                $this->addFlash('success', "El comp $comprobante ahora se encuentra Vigente! ");
                return $this->redirectToRoute('compestab_index');
            }
            else{
                $this->addFlash('error', "<i class='fa fa-warning'></i>El comp $comprobante No se encuentraba pendiente de entrega ");
            }
        }
        return $this->render("compestab/entrega.html.twig",[
            'compestab'=>$comprobante,
            'servicios'=>$servicios,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/{id}/suspender", name="compestab_suspender")
     */
    public function suspenderCompEstab(Request $request, Compestab $comprobante){
        $em= $this->getDoctrine()->getManager();
        $form = $this->createForm( CompestabType::class, $comprobante);
        $form->handleRequest($request);
        $causas= $em->getRepository(CausaSuspensionComp::class)->findAll();
        if($request->getMethod()=="POST"){
            $estado= $comprobante->getEstadoComp()->getId();
            if($estado ==4 ){
                $fecha= $request->get('fechasusp');
                $causac= $request->get('causasuspencion');
                if ($fecha && $causac){
                    $comprobante->setFinicioSusp(\DateTime::createFromFormat('d/m/Y',$fecha));
                    $comprobante->setCSuspencion($em->getRepository(CausaSuspensionComp::class)->find($causac));
                    $comprobante->setEstadoComp($em->getRepository(NombEstComp::class)->find(6));
                    $em->persist($comprobante);
                    $em->flush();
                    $this->addFlash('success', "El comp $comprobante ahora se encuentra Suspendido! ");
                    return $this->redirectToRoute('compestab_index');
                }
            }
            else{
                $this->addFlash('error', "El comp $comprobante no puede ser suspendido. El mismo no está Vigente!");
            }
        }
        return $this->render('compestab/suspender.html.twig',[
            'compestab'=>$comprobante,
            'form'=>$form->createView(),
            'causas'=>$causas,
        ]);
    }

    /**
     * @Route("/{id}/duplicado", name="compestab_duplicado")
     */
    public function duplicado(Compestab $comprobante, Request $request){
        if($comprobante->getEstadoComp()->getId()==4){
            $em= $this->getDoctrine()->getManager();
            $duplicado = new Compestab();
            $duplicado->setEstadoComp($em->getRepository(NombEstComp::class)->find(1));
            $duplicado->setDuplicado(true);
            $duplicado->setFemitido(new \DateTime('today'));
            $duplicado->setFvencimiento($comprobante->getFvencimiento());
            $duplicado->setExtensionID($comprobante->getExtensionID());
            $form = $this->createForm(CompestabType::class, $duplicado);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $comprobante->setEstadoComp($em->getRepository(NombEstComp::class)->find(8));
                $comprobante->setCCancel($em->getRepository(CausaCancelacionComp::class)->find(10));
                $comprobante->setFcancel(new \DateTime('today'));
                $duplicado->setInstalacion($comprobante->getInstalacion());
                $duplicado->setEstadoComp($em->getRepository(NombEstComp::class)->find(2));
                $duplicado->setImporte(MainController::Tarifa($em,'Comprobantes y Duplicados',$duplicado->getInstalacion()->getLot()->getIdextension())->getValor());
                $em->persist($comprobante);
                $em->persist($duplicado);
                $em->flush();
                $this->addFlash('success', "El duplicado del comprobante $comprobante ahora se encuentra Pendiente de Impresion! ");
                return $this->redirectToRoute('compestab_ptesimpress');
            }
            return $this->render('compestab/duplicar.html.twig',[
                'compestab'=>$comprobante,
                'duplicado'=>$duplicado,
                'form'=>$form->createView(),
                'instalacion'=>$comprobante->getInstalacion()
            ]);
        }
        $this->addFlash('error', "El comprobante $comprobante debe estar Vigente para realizarle un Duplicado!");
        return $this->redirectToRoute('compestab_index');
    }

    /**
     * @Route("/{id}/cancelar", name="compestab_cancelar")
     */
    public function cancelarCompEstab(Request $request, Compestab $comprobante){
        $em= $this->getDoctrine()->getManager();
        $form = $this->createForm(CompestabType::class, $comprobante);
        $form->handleRequest($request);
        $causas= $em->getRepository(CausaCancelacionComp::class)->findAll();
        if($request->getMethod()=="POST"){
            $estado= $comprobante->getEstadoComp()->getId();//dump($request);
            if($estado == 4 || $estado == 6 || $estado == 7  ){
                $fecha= $request->get('fechacancel');
                $causac= $request->get('causacancel');
                if ($fecha && $causac){
                    $comprobante->setFcancel(\DateTime::createFromFormat('d/m/Y',$fecha));
                    $comprobante->setCCancel($em->getRepository(CausaCancelacionComp::class)->find($causac));
                    $comprobante->setEstadoComp($em->getRepository(NombEstComp::class)->find(8));
                    $em->persist($comprobante);
                    $em->flush();
                    $this->addFlash('success', "El comp $comprobante ahora se encuentra Cancelado! ");
                    return $this->redirectToRoute('compestab_index');
                }
            }
        }
        return $this->render('compestab/cancelar.html.twig',[
            'compestab'=>$comprobante,
            'form'=>$form->createView(),
            'causas'=>$causas,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="compestab_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Compestab $compestab): Response
    {
        $form = $this->createForm(CompestabType::class, $compestab);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('compestab_index');
        }

        return $this->render('compestab/edit.html.twig', [
            'compestab' => $compestab,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}", name="compestab_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Compestab $compestab): Response
    {
        if ($this->isCsrfTokenValid('delete'.$compestab->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($compestab);
            $entityManager->flush();
        }

        return $this->redirectToRoute('compestab_index');
    }
}
