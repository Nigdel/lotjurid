<?php

namespace App\Controller;

use App\Entity\CausaCancelacionComp;
use App\Entity\CausaSuspensionComp;
use App\Entity\Comprobante;
use App\Entity\Extension;
use App\Entity\MediosTrans;
use App\Entity\NombEstComp;
use App\Entity\Tarifa;
use App\Form\ComprobanteType;
use App\Repository\ComprobanteRepository;
use App\Repository\ExtensionRepository;
use App\Repository\TarifaRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\MainController;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/comprobante")
 */
class ComprobanteController extends AbstractController
{
    /**
     * @Route("/", name="comprobante_index", methods={"GET"})
     */
    public function index(ComprobanteRepository $comprobanteRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = null;
        $medio= null;
        $folio=null;
        $search = $request->query->get('medio',null);
        if ($search) {
            $queryBuilder = $comprobanteRepository->searchMedio($search);
            $medio= $search;
        } else{
            $search = $request->query->get('folio',null);
            if ($search) {
                $queryBuilder = $comprobanteRepository->searchFolio($search);
                $folio= $search;
            }else{
              $queryBuilder = $comprobanteRepository->getWithSearchQueryBuilder();
            }
        }

        //dump($request);
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('comprobante/index.html.twig', [
            /*'comprobantes' => $comprobanteRepository->findAll(),*/
            'pagination' => $pagination,
            'medio'=>$medio,
            'folio'=>$folio,
        ]);
    }

    /**
     * @Route("/new", name="comprobante_new", methods={"GET","POST"})
     */
    public function new(Request $request, Security $security): Response
    {
        if($security->isGranted('ROLE_ADMIN')){
            $comprobante = new Comprobante();
            $form = $this->createForm(ComprobanteType::class, $comprobante);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comprobante);
                $entityManager->flush();

                return $this->redirectToRoute('comprobante_index');
            }

            return $this->render('comprobante/new.html.twig', [
                'comprobante' => $comprobante,
                'form' => $form->createView(),
            ]);
        }
        else{
            $this->addFlash('error', 'Esta ruta no esta disponible para usted');
        }
    }
    /**
     * @Route("/newbymed/{medio}", name="comprobante_newbymed", methods={"GET","POST"})
     */
    public function newbymed(Request $request, MediosTrans $medio, TarifaRepository $tarifaRepository, ExtensionRepository $extensionRepository): Response
    {
        $UltimComp = $medio->getComprobanteActivo();
        if($UltimComp){
            if($UltimComp->getEstadoComp()->getNombreEstado()=="Pendiente de Impresion"){
                $this->addFlash('error','El medio tiene un comprobante pendiente de impresion. No se puede realizar la operacion');
                return $this->redirectToRoute('medios_trans_show',['id'=>$medio->getId()]);
            }
            if($UltimComp->getEstadoComp()->getNombreEstado()=="Suspendido"){
                $this->addFlash('error','El medio tiene un comprobante Suspendido. No se puede realizar la operacion');
                return $this->redirectToRoute('medios_trans_show',['id'=>$medio->getId()]);
            }
        }
        $tarifas= $tarifaRepository->findBy(['concepto'=>'Comprobantes y Duplicados']);
        $res=$this->container->get('serializer')->serialize($tarifas, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ]));
        $comprobante = new Comprobante();
        $entityManager = $this->getDoctrine()->getManager();
        $comprobante->setFemitido(new \DateTime('today'));
        $comprobante->setLot($medio->getBasificacionObj()->getIdlicencia());
        $comprobante->setEstadoComp($entityManager->getRepository(NombEstComp::class)->find(1));
        $comprobante->setExtension($comprobante->getLot()->getIdextension());
        $exts= null;
        if($medio->getRama()->getId()!=2)
        $exts = $extensionRepository->findTerrestre();
        if($medio->getAseguramiento())
            $comprobante->setExtension($extensionRepository->find(1));
        $form = $this->createForm(ComprobanteType::class, $comprobante,['exts'=>$exts]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comprobante->setMedio($medio);
            $comprobante->setEstadoComp($entityManager->getRepository(NombEstComp::class)->find(2));
            $importe= (MainController::Tarifa($entityManager,'Comprobantes y Duplicados',$comprobante->getExtension()))->getValor();
            if($importe!= $comprobante->getImporte()){
                $this->addFlash('error',"Se ha ajustado el importe del comprobante $importe segun bd ".$comprobante->getImporte()." segun formulario");
                $comprobante->setImporte($importe);
            }
            $nuevafecha = new \DateTime($comprobante->getFemitido()->format('Y-m-d'));
            $comprobante->setFvencimiento( $nuevafecha->add(new \DateInterval('P3Y')));
            $cancelado= $entityManager->getRepository(NombEstComp::class)->findOneBy(['nombreEstado'=>'Cancelado']);
            if($UltimComp){
                $UltimComp->setEstadoComp($cancelado);
                $entityManager->persist($UltimComp);
            }
               
            $entityManager->persist($comprobante);
           

            $entityManager->flush();
            $this->addFlash('success', "El comp $comprobante ahora se encuentra Pendiente de Impresion! ");
            return $this->redirectToRoute('comprobante_ptesimpress');
        }

        return $this->render('comprobante/new.html.twig', [
            'comprobante' => $comprobante,
            'form' => $form->createView(),
            'medio' => $medio,
            'tarifas'=>$res,

        ]);
    }

    /**
     * @Route("/{id}/imprimir/{folio}", name="comprobante_imprimir")
     */
    public function imprimir(Request $request, Comprobante $comprobante,string $folio){
        $em = $this->getDoctrine()->getManager();
        if($folio !== null && $comprobante->getEstadoComp()->getId()==2){
            //revisr que el folio no se haya asignado a otro comp
            $existe= $em->getRepository(Comprobante::class)->findOneBy(['folio'=>$folio]);
            if($existe===null|| $folio == $comprobante->getFolio()){
                $comprobante->setFolio($folio);
                $comprobante->setFimpreso(new \DateTime('today'));
                $prov= $comprobante->getMedio()->getBasificacionObj()->getIdmun()->getProvinciaid()->getId();
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

                return $this->render('comprobante/comppdf.html.twig', [
                    'title' => "comp de medio de transporte $comp",'comp'=>$comp,'folio'=>$folio
                ]);
            }
            else{
                $this->addFlash('error',"No se puede asignar el mismo folio a mas de un comprobante");
                return $this->redirectToRoute('comprobante_index');
            }

        }
        else
        {
            $this->addFlash('error', "La lot $comprobante no tiene un folio v&aacute;lido asignado o no se ha imprimido aun");
            return $this->redirectToRoute('comprobante_index');
        }
    }

    /**
     * @Route("/{id}/download/{folio}",name="comprobante_descargar")
     */
    public function descargar(Request $request, Comprobante $comprobante,string $folio){
        $em = $this->getDoctrine()->getManager();
        if($folio !== null && $comprobante->getEstadoComp()->getId()==2){
            //revisr que el folio no se haya asignado a otro comp
            $existe= $em->getRepository(Comprobante::class)->findOneBy(['folio'=>$folio]);
            if($existe===null|| $folio == $comprobante->getFolio()){

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
                $pdfOptions->set('defaultFont', 'Serif');
                $pdfOptions->set('defaultMediaType','print');
//                $customPaper1= array(0,0,408.30,289);
                $pdfOptions->set('defaultPaperOrientation','portrait');
//                $pdfOptions->set('defaultPaperSize',$customPaper1);

                // Instantiate Dompdf with our options
                $dompdf = new Dompdf($pdfOptions);

                // Retrieve the HTML generated in our twig file
                $html = $this->renderView('comprobante/comppdf-bk.html.twig', [
                    'title' => "comp de medio de transporte $comp",'comp'=>$comp,'folio'=>$folio
                ]);

                $filename=$comp->__toString();
                // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
                $customPaper= array(0,0,408.30,289);
//                $dompdf->setPaper('lotcomp','portrait');
                $dompdf->setPaper($customPaper);
                // Load HTML to Dompdf
                $dompdf->loadHtml($html);

                // Render the HTML as PDF
                $dompdf->render();

                // Output the generated PDF to Browser (force download)
                $dompdf->stream($filename, [
                    "Attachment" => false
                ]);
            }
            else{
                $this->addFlash('error',"No se puede asignar el mismo folio a mas de un comprobante");
                return $this->redirectToRoute('comprobante_index');
            }

        }
        else
        {
            $this->addFlash('error', "La lot $comprobante no tiene un folio v&aacute;lido asignado o no se ha imprimido aun");
            return $this->redirectToRoute('comprobante_index');
        }
    }

    /**
     * @Route("/{id}/entregar", name="comprobante_entregar")
     */
    public function entregar(Request $request,Comprobante $comprobante):Response{
        $em= $this->getDoctrine()->getManager();
        $form = $this->createForm(ComprobanteType::class, $comprobante);
        $form->handleRequest($request);
        if($request->getMethod()=="POST"){
            if($comprobante->getEstadoComp()->getId()==3){
                $comprobante->setFentrega(\DateTime::createFromFormat('d/m/Y',$request->get("fechaentrega","today")));
                $comprobante->setEstadoComp($em->getRepository(NombEstComp::class)->find(4));
                $comprobante->setImporte(MainController::Tarifa($em,'Comprobantes y Duplicados',$comprobante->getExtension())->getValor());
                $em->persist($comprobante);
                $em->flush();
                $this->addFlash('success', "El comp  $comprobante ahora se encuentra Vigente! ");
                return $this->redirectToRoute('comprobante_index');
            }
            else{
                $this->addFlash('error', "<i class='fa fa-warning'></i>El comp $comprobante No se encuentraba pendiente de entrega ");
            }
        }
        return $this->render("comprobante/entrega.html.twig",[
            'comp'=>$comprobante,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/{id}/cancelar",name="comprobante_cancelar")
     */
    public function cancelar(Request $request, Comprobante $comprobante){
        $em= $this->getDoctrine()->getManager();
        $form = $this->createForm(ComprobanteType::class, $comprobante);
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
                    return $this->redirectToRoute('comprobante_index');
                }
            }
        }
        return $this->render('comprobante/cancelar.html.twig',[
            'comp'=>$comprobante,
            'form'=>$form->createView(),
            'causas'=>$causas,
        ]);
    }

    /**
     * @Route("/{id}/suspender", name="comprobante_suspender")
     */
    public function suspenderComp(Request $request, Comprobante $comprobante){
        $em= $this->getDoctrine()->getManager();
        $form = $this->createForm( ComprobanteType::class, $comprobante);
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
                    $comprobante->setFfinSusp(null);
                    $em->persist($comprobante);
                    $em->flush();
                    $this->addFlash('success', "El comp $comprobante ahora se encuentra Suspendido! ");
                    return $this->redirectToRoute('comprobante_show',['id'=>$comprobante->getId()]);
                }
            }
            else{
                $this->addFlash('error', "El comp $comprobante no puede ser suspendido. El mismo no está Vigente!");
            }
        }
        return $this->render('comprobante/suspender.html.twig',[
            'comp'=>$comprobante,
            'form'=>$form->createView(),
            'causas'=>$causas,
        ]);
    }

    /**
     * @Route("/{id}/finsuspension", name="comprobante_finsuspension")
     * @throws \Exception
     */
    public function finsuspension(Request $request, Comprobante $comprobante){
        $em= $this->getDoctrine()->getManager();
        $form = $this->createForm( ComprobanteType::class, $comprobante);
        $form->handleRequest($request);

        if($request->getMethod()=="POST"){
            $estado= $comprobante->getEstadoComp()->getId();
            if($estado ==6 ){
                $fecha= $request->get('fechasusp');
                $reponer= $request->get('reponerT');
                $date1 = $comprobante->getFinicioSusp();
                $date2= \DateTime::createFromFormat('d/m/Y',$fecha);
                if ($date2 && ($date2 > $date1) ){
                    $comprobante->setFfinSusp($date2);
                    $comprobante->setEstadoComp($em->getRepository(NombEstComp::class)->find(4));
                    if($reponer==='on'){
                        $diff = $date1->diff($date2)->days;
                        $venc= $comprobante->getFvencimiento();
                        $venc->add(new \DateInterval('P'.$diff.'D'));
//                        dump($venc);
                        $comprobante->setFvencimiento($venc);

                        $em->persist($comprobante);
                        $em->flush();
//                        dump($comprobante);
                        $this->addFlash('success', "El comp $comprobante ahora se encuentra Vigente. Se ha extendido su fecha de vencimiento en $diff dias.");
                    }
                    else{
                        $this->addFlash('success', "El comp $comprobante ahora se encuentra Vigente! ");
                    }
                    $em->persist($comprobante);
                    $em->flush();
//                    return $this->redirectToRoute('comprobante_show',['id'=>$comprobante->getId()]);
                }else{
                    $this->addFlash('error', "El comp $comprobante no puede ser Rehabilitado. La fecha de incio de suspension no puede ser mayor a la de fin de suspension");
                }
            }
            else{
                $this->addFlash('error', "El comp $comprobante no puede ser Rehabilitado. El mismo no está Suspendido!");
            }
        }
        return $this->render('comprobante/finsuspension.html.twig',[
            'comp'=>$comprobante,
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/marcarcomoimpreso", name="comprobante_marcarcomoimpreso")
     */
    public function marcarComoImpreso(Comprobante $comprobante,Request $request){
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
        return $this->redirectToRoute('comprobante_ptesentrega');
    }

    /**
     * @Route("/{id}/duplicado", name="comprobante_duplicado")
     */
    public function duplicado(Comprobante $comprobante, Request $request){
        if($comprobante->getEstadoComp()->getId()==4){
            $em= $this->getDoctrine()->getManager();
            $duplicado = new Comprobante();
            $duplicado->setExtension($comprobante->getExtension());
            $duplicado->setEstadoComp($em->getRepository(NombEstComp::class)->find(1));
            $duplicado->setDuplicado(true);
            $duplicado->setFemitido(new \DateTime('today'));
            $duplicado->setFvencimiento($comprobante->getFvencimiento());
            $duplicado->setLot($comprobante->getLot());

            $form = $this->createForm(ComprobanteType::class, $duplicado);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $comprobante->setEstadoComp($em->getRepository(NombEstComp::class)->find(8));
                $comprobante->setCCancel($em->getRepository(CausaCancelacionComp::class)->find(10));
                $comprobante->setFcancel(new \DateTime('today'));
                $duplicado->setMedio($comprobante->getMedio());
                $duplicado->setEstadoComp($em->getRepository(NombEstComp::class)->find(2));
                $duplicado->setImporte((MainController::Tarifa($em,'Comprobantes y Duplicados',$duplicado->getExtension()))->getValor());
                $em->persist($comprobante);
                $em->persist($duplicado);
                $em->flush();
                $medio= $comprobante->getMedio();
                $this->addFlash('success', "El duplicado del comprobante del medio $medio  ahora se encuentra Pendiente de Impresion! ");
                return $this->redirectToRoute('comprobante_ptesimpress');
            }

            return $this->render('comprobante/duplicar.html.twig',[
                'comprobante'=>$duplicado,
                'form'=>$form->createView(),
                'medio'=>$comprobante->getMedio()
            ]);
        }
        $this->addFlash('error', "El comprobante $comprobante debe estar Vigente para realizarle un Duplicado!");
        return $this->redirectToRoute('comprobante_index');

    }

    /**
     * @Route("/ptesimpres",name="comprobante_ptesimpress")
     * @param ComprobanteRepository $comprobanteRepository
     * @return Response
     */
    public function ptesImpress(ComprobanteRepository $comprobanteRepository,Request $request){
        if($request->getMethod()=="POST"){
            $folio= $request->get('foliovalor');
            $comp= $request->get('comp');
            return $this->redirectToRoute("comprobante_imprimir",['id'=>$comp,'folio'=>$folio]);
        }
        
        return $this->render('comprobante/tramiteCompbase.html.twig',[
            'tramitecomp'=>"Pendientes de impresion",
            'comps'=>$comprobanteRepository->findConCompPtesImpres($this->getUser())
        ]);
    }

    /**
     * @Route("/ptesentrega",name="comprobante_ptesentrega")
     * @param ComprobanteRepository $comprobanteRepository
     * @return Response
     */
    public function ptesEntrega(ComprobanteRepository $comprobanteRepository){
        return $this->render('comprobante/tramiteCompbase.html.twig',[
            'tramitecomp'=>"Pendientes de entrega",
            'comps'=>$comprobanteRepository->findConCompPtesEntrega($this->getUser())
        ]);
    }

    /**
     * @Route("/retirados",name="comprobante_retirados")
     * @param ComprobanteRepository $comprobanteRepository
     * @return Response
     */
    public function ocupados(ComprobanteRepository $comprobanteRepository){
        return $this->render('comprobante/tramiteCompbase.html.twig',[
            'tramitecomp'=>"Retirados",
            'comps'=>$comprobanteRepository->findBy(['estadoComp'=>6])
        ]);
    }

    /**
     * @Route("/{id}", name="comprobante_show", methods={"GET"})
     */
    public function show(Comprobante $comprobante): Response
    {
        return $this->render('comprobante/show.html.twig', [
            'comprobante' => $comprobante,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="comprobante_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Comprobante $comprobante, TarifaRepository $tarifaRepository, ExtensionRepository $extensionRepository): Response
    {
        $user= $this->getUser();
        if($comprobante->editablebyme($user)){
            if($comprobante->getMedio()->getRama()->getId()!=2)
                $exts = $extensionRepository->findTerrestre();
            if($comprobante->getMedio()->getAseguramiento())
                $comprobante->setExtension($extensionRepository->find(1));
            $form = $this->createForm(ComprobanteType::class, $comprobante,['exts'=>$exts]);
            $form->handleRequest($request);
            $tarifas= $tarifaRepository->findBy(['concepto'=>'Comprobantes y Duplicados']);
            $res=$this->container->get('serializer')->serialize($tarifas, 'json', array_merge([
                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
            ]));
            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success','Se ha modificado el comprobante correctamente');
                return $this->redirectToRoute('comprobante_index');
            }

            return $this->render('comprobante/edit.html.twig', [
                'comprobante' => $comprobante,
                'form' => $form->createView(),
                'tarifas'=> $res,
            ]);
        }
        else{
            $this->addFlash('error','El comprobante no se encuentra Pendiente de Impresion o no puede ser editado por usted. Solo los comprobantes pendientes de impresion pueden ser modificados por funcionarios del municipio donde se encuentra basificado el medio.');
            return $this->redirectToRoute('comprobante_show',['id'=>$comprobante->getId()]);
        }


    }

    /**
     * @Route("/{id}", name="comprobante_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Comprobante $comprobante): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comprobante->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comprobante);
            $entityManager->flush();
        }

        return $this->redirectToRoute('comprobante_index');
    }
}
