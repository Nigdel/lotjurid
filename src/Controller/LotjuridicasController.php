<?php

namespace App\Controller;
use App\Entity\CausaCancelacionLot;
use App\Entity\CausaSuspensionLot;
use App\Entity\EstadoLot;
use App\Entity\EstadoTramite;
use App\Entity\Lotjuridicas;
use App\Entity\Municipios;
use App\Entity\Personasjuridicas;
use App\Entity\Provincias;
use App\Entity\Ramas;
use App\Entity\TipoServicio;
use App\Entity\Tipotramite;
use App\Entity\Tramite;
use App\Entity\User;
use App\Form\LotjuridicasType;
use App\Repository\EstadoTramiteRepository;
use App\Repository\LotjuridicasRepository;
use App\Repository\TarifaRepository;
use App\Repository\TipotramiteRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use function MongoDB\BSON\toJSON;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/lotjuridicas")
 */
class LotjuridicasController extends AbstractController
{
    /**
     * @Route("/", name="lotjuridicas_index", methods={"GET"})
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(LotjuridicasRepository $repository, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = null;
        $pj=null;
        $lot=null;
        $search = $request->query->get('lot');
        if ($search) {
            $queryBuilder = $repository->searchLot($search);
            $lot= $search;
        }else{
            $search = $request->query->get('pj');
            if ($search){
                $queryBuilder = $repository->searchByPj($search);
                $pj=$search;
            }
            else
                $queryBuilder = $repository->getWithSearchQueryBuilder($this->getUser());
        }
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('lotjuridicas/index.html.twig', [
            'pagination' => $pagination,
            'pj'=>$pj,
            'lot'=>$lot,
        ]);
    }

    /**
     * @Route("/new", name="lotjuridicas_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $usr= $this->getUser();
        if($this->isGranted('ROLE_ADMIN',$usr)){
            $lotjuridica = new Lotjuridicas();
            $form = $this->createForm(LotjuridicasType::class, $lotjuridica);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $lotjuridica->setImporte(MainController::TarifaLot($entityManager,$lotjuridica)->getValor());
                $entityManager->persist($lotjuridica);
                $entityManager->flush();

                return $this->redirectToRoute('lotjuridicas_index');
            }

            return $this->render('lotjuridicas/new-.html.twig', [
                'lotjuridica' => $lotjuridica,
                'form' => $form->createView(),
            ]);
        }
        else{
            $this->addFlash('error','Solo los administradores del sitio tienen acceso a esa ruta');
            return $this->redirectToRoute('portada');
        }
    }
    /**
     * @Route("/newbypj/{pj}", name="lotjuridicas_newbypj", methods={"GET","POST"})
     */
    public function newbypj(Request $request, Personasjuridicas $pj, TipotramiteRepository $tt, EstadoTramiteRepository $et,TarifaRepository $tarifaRepository): Response
    {
        $em= $this->getDoctrine()->getManager();
        $lotjuridica = new Lotjuridicas();
        $lotjuridica->setIdentidad($pj)
            ->setId($pj->getId())
            ->setIdestado($this->getDoctrine()->getRepository(EstadoLot::class)->find(1))
            ->setIdtramitador($this->getUser())
            ->setIdaprueba($this->DetAprobacion($lotjuridica))
        ;
        $tarifas= $tarifaRepository->all();
        $res=$this->container->get('serializer')->serialize($tarifas, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ]));
        $tramite = new Tramite();
        $tramite->setUsuario($this->getUser())
            ->setTipotramite($tt->find(2))
            ->setLot($lotjuridica)
            ->setEstado($et->find(1))
            ->setFecha(new \DateTime());
        try{
            $this->denyAccessUnlessGranted('LOT_NEW', $tramite,"Usted no tiene permiso para crear una lot de una entidad perteneciente a un municipio distinto al suyo");
        }catch (AccessDeniedException $exception){
            $this->addFlash('error',$exception->getMessage());
            return $this->redirectToRoute('personasjuridicas_show',['id'=>$pj->getId()]);
        }
        $form = $this->createForm(LotjuridicasType::class, $lotjuridica);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lotprevia = $em->getRepository(Lotjuridicas::class)->findOneBy([
                    'identidad'=>$lotjuridica->getIdentidad(),
                    'idservicio'=>$lotjuridica->getIdservicio(),
                    'idrama'=>$lotjuridica->getIdrama(),
                    'idtipo'=>$lotjuridica->getIdtipo()
                ]);
            $exp = $em->getRepository(Lotjuridicas::class)->find($lotjuridica->getId());
            if($exp!==null){
//                $url= $this->generateUrl('lotjuridicas_show',['id' => $exp->getid() ]);
                $this->addFlash('error',"El numero de expediente seleccionado ya ha sido asignado a otra lot. Verifique que es el correcto.");
                return $this->render('lotjuridicas/new-.html.twig', [
                    'lotjuridica' => $lotjuridica,
                    'form' => $form->createView(),
                    'tarifas'=>$res,
                ]);
            }
            if($lotprevia!==null){
                $url= $this->generateUrl('lotjuridicas_show',['id' => $lotprevia->getid() ]);
                $this->addFlash('error',"La entidad ya cuenta con la lot $lotprevia, la misma coincide en Tipo, servicio y rama con la que pretende solicitar.");
                return $this->render('lotjuridicas/new-.html.twig', [
                    'lotjuridica' => $lotjuridica,
                    'form' => $form->createView(),
                    'tarifas'=>$res,
                ]);
            }
            $lotjuridica->setImporte(MainController::TarifaLot($em,$lotjuridica)->getValor());
            $tramite->setEstado($et->find(3));
            $em->persist($tramite);
            $em->persist($lotjuridica);
            $em->flush();
            $this->addFlash('success', "La lot $lotjuridica ahora se encuentra Pendiente de Aprobacion! ");
            return $this->redirectToRoute('lotjuridicas_index');
        }

        return $this->render('lotjuridicas/new-.html.twig', [
            'lotjuridica' => $lotjuridica,
            'form' => $form->createView(),
            'tarifas'=>$res,
        ]);
    }
    /**
     * @Route("/newtramit",name="lotjuridicas_newsolic",methods={"GET","POST"})
     */
    public function newSolicitud(Request $request): Response{
        $lotjuridica = new Lotjuridicas();
        $numlot= $request->get('numlot',null);
        if($numlot!==null){
            $lot = $this->getDoctrine()->getManager()->getRepository(Lotjuridicas::class)->find($numlot);
            if ($lot!==null){
                return $this->redirectToRoute('lotjuridicas_newtramit',['id'=>$lot->getId()]);
            }
            else{
                $lotjuridica->setId($numlot);
                $form = $this->createForm(LotjuridicasType::class, $lotjuridica);
                $form->handleRequest($request);
                return $this->render('lotjuridicas/nuevaSol.html.twig', [
                    'lotjuridica' => $lotjuridica,
                    'form' => $form->createView(),
                ]);
            }
        }
        $form = $this->createForm(LotjuridicasType::class, $lotjuridica);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lotjuridica);
            $entityManager->flush();

            return $this->redirectToRoute('lotjuridicas_index');
        }

        return $this->render('lotjuridicas/nuevaSol.html.twig', [
            'lotjuridica' => $lotjuridica,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/pteaprob", name="lotjuridicas_pteAprob" )
     */
    public function pteAprob()
    {
        $em = $this->getDoctrine()->getManager();
        $ptes=$em->getRepository(Lotjuridicas::class)->findBy(['idestado'=>1]);
        return $this->render('lotjuridicas/tramitelotbase.html.twig',[
            'tramitelot'=>"Pendientes de Aprobación",
            'lots'=>$ptes
        ]);
    }

    /**
     * @Route("/ptesentrega", name="lotjuridicas_ptesentrega")
     */
    public function ptesentrega(){
        $em = $this->getDoctrine()->getManager();
        $ptes=$em->getRepository(Lotjuridicas::class)->findBy(['idestado'=>3]);
        return $this->render('lotjuridicas/tramitelotbase.html.twig',[
            'tramitelot'=>"Pendientes de Entrega",
            'lots'=>$ptes
        ]);
    }
    /**
     * @Route("/ptesimpresion", name="lotjuridicas_ptesimpresion")
     */
    public function ptesImpresion(Request $request){
        if($request->getMethod()=="POST"){
//            dump($request);
            $folio= $request->get('foliovalor');
            $lot= $request->get('lot');
            return $this->redirectToRoute("lotjuridicas_imprimir",['id'=>$lot,'folio'=>$folio]);
        }

        $em = $this->getDoctrine()->getManager();
        $ptes=$em->getRepository(Lotjuridicas::class)->findBy(['idestado'=>11]);
        return $this->render('lotjuridicas/tramitelotbase.html.twig',[
            'tramitelot'=>"Pendientes de Impresion",
            'lots'=>$ptes
        ]);
    }

    /**
     * @Route("/canceladas", name="lotjuridicas_canceladas" )
     */
    public function canceladas(LotjuridicasRepository $repository,Request $request, PaginatorInterface $paginator){
        $queryBuilder = null;
        $pj=null;
        $lot=null;

        $prov= $this->getUser()->getMunicipio()->getProvinciaid();
        $concepto="Canceladas de $prov";
        $search = $request->query->get('lot');
        if ($search) {
            $queryBuilder = $repository->searchLot($search);
            $lot= $search;
            $concepto= "Busqueda por expediente";
        }else{
            $search = $request->query->get('pj');
            if ($search){
                $queryBuilder = $repository->searchByPj($search);
                $pj=$search;
                $concepto= "Busqueda Por PJ";
            }
            else
                $queryBuilder = $repository->lotsCanceladas($prov);
        }

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('lotjuridicas/index.html.twig', [
            'pagination' => $pagination,
            'pj'=>$pj,
            'lot'=>$lot,
            'concepto'=>$concepto,
        ]);
    }

    /**
     * @Route("/canceladasBA", name="lotjuridicas_canceladasBasifAqui" )
     */
    public function canceladasBasifAqui(LotjuridicasRepository $repository,Request $request, PaginatorInterface $paginator){
        $queryBuilder = null;
        $pj=null;
        $lot=null;
        $prov= $this->getUser()->getMunicipio()->getProvinciaid();
        $concepto="Canceladas con basif aquí";
        $search = $request->query->get('lot');
        if ($search) {
            $queryBuilder = $repository->searchLot($search);
            $lot= $search;
            $concepto= "Busqueda por expediente";
        }else{
            $search = $request->query->get('pj');
            if ($search){
                $queryBuilder = $repository->searchByPj($search);
                $pj=$search;
                $concepto= "Busqueda Por PJ";
            }
            else
                $queryBuilder = $repository->lotsCanceladasConBasifenProv($prov);
        }

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('lotjuridicas/index.html.twig', [
            'pagination' => $pagination,
            'pj'=>$pj,
            'lot'=>$lot,
            'concepto'=>$concepto,
        ]);
    }

    /**
     * @Route("/ptesrenovacion", name="lotjuridicas_ptesrenovacion")
     * @param Request $request
     * @return Response
     */
    public function ptesRenovacion(Request $request){
        $em = $this->getDoctrine()->getManager();
        $ptes=$em->getRepository(Lotjuridicas::class)->findBy(['idestado'=>[6,7,8]]);
        return $this->render('lotjuridicas/tramitelotbase.html.twig',[
            'tramitelot'=>"Pendientes de Renovacion",
            'lots'=>$ptes
        ]);
    }

    /**
     * @Route("/lotsbyme", name="lotjuridicas_trambyme")
     */
    public function lotsbyme(LotjuridicasRepository $repository, Request $request, PaginatorInterface $paginator){
        $queryBuilder = null;
        $pj=null;
        $lot=null;
        $search = $request->query->get('lot');
        if ($search) {
            $queryBuilder = $repository->searchLot($search);
            $lot= $search;
        }else{
            $search = $request->query->get('pj');
            if ($search){
                $queryBuilder = $repository->searchByPj($search);
                $pj=$search;
            }
            else
                $queryBuilder = $repository->lotsTramitadasbyUser($this->getUser());
        }
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('lotjuridicas/index.html.twig', [
            'pagination' => $pagination,
            'pj'=>$pj,
            'lot'=>$lot,
            'concepto'=>"tramitadas por mi",
        ]);
    }

    /**
     * @Route("/lotsapbyme", name="lotjuridicas_aprobbyme")
     */
    public function lotsAprobbyme(LotjuridicasRepository $repository, Request $request, PaginatorInterface $paginator){
        $queryBuilder = null;
        $pj=null;
        $lot=null;
        $usr= $this->getUser();
        $search = $request->query->get('lot');
        if ($search) {
            $queryBuilder = $repository->searchLot($search);
            $lot= $search;
        }else{
            $search = $request->query->get('pj');
            if ($search){
                $queryBuilder = $repository->searchByPj($search);
                $pj=$search;
            }
            else
                $queryBuilder = $repository->lotsAprobbyUser($usr);
        }
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('lotjuridicas/index.html.twig', [
            'pagination' => $pagination,
            'pj'=>$pj,
            'lot'=>$lot,
            'concepto'=>"aprobadas por mi",
        ]);
    }

    /**
     * @Route("/vig/{tipo}/{rama}", name="lotjuridicas_vigTipoRama")
     */
    public function vigTipoRama(TipoServicio $tipo, Ramas $rama, LotjuridicasRepository $repository, Request $request, PaginatorInterface $paginator){
        $queryBuilder = null;
        $pj=null;
        $lot=null;
        $usr= $this->getUser();
        $search = $request->query->get('lot');
        if ($search) {
            $queryBuilder = $repository->searchLot($search);
            $lot= $search;
        }else{
            $search = $request->query->get('pj');
            if ($search){
                $queryBuilder = $repository->searchByPj($search);
                $pj=$search;
            }
            else{
                $queryBuilder = $repository->lotsVig($rama,$tipo);
            }
        }
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('lotjuridicas/index.html.twig', [
            'pagination' => $pagination,
            'pj'=>$pj,
            'lot'=>$lot,
            'concepto'=>"Vigentes por Tipo de Servicio($tipo) y Rama($rama)",
        ]);
    }

    /**
     * @Route("/export/{prov}", name="lotjuridicas_export_prov")
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportListado(Provincias $prov){
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT l.`NuLicencia`,pj.`NomEntidad`,e.`Extension`, r.`Ramas`, est.`nombreEstado`, l.`causadecancelacion_id`, l.`causa_suspension_id`,  l.`FechaSolicitud`,l. `Presentada`,
l. `Aprobada`,l. `FechaAprobacion`, l.`FechaEmision`, l.`FechaEntrega`, l.`limitacion`,
l.`TpoMedioAmparado`,  l.`FechaDeCancelacion`, l.`FechaRenov`,l. `MediaTarifa`, 
l.`NumFolio`,l. `FechaAprobInicial`,l. `Dictamen`, l.`c_negacion`, l.`ProrrogadoEnDias`,
l. `FechaDeDestruccion`, l.`Duplicado`,l. `fimpresion`,l. `fecha_suspension`,l. `importe`,
tl.`tipodelot`, ts.`TipoServicio`, sa.`ServicioAmparado` 
FROM `lotjuridicas` l inner join 
extension e on(l.`idextension`=e.id) inner join ramas r on(r.id = l.idrama) 
inner join personasjuridicas pj on(l.identidad = pj.identidad) 
inner join estado_lot est on(est.id = l.`IDEstado`)inner join municipios m on(pj.idmunicipio = m.ID)
inner join tipo_lot tl on(tl.id = l.`IDTipo`) inner join tipo_servicio ts on(ts.id =l.`IDServicio` )
inner join servicio_amparado sa on(sa.id = l.`ServicioAmparado`) where m.ProvinciaID=:prov;";
        $stmt = $db->prepare($sql);

        $params = array('prov'=>$prov->getId());
        $stmt->execute($params);
        $resumen= $stmt->fetchAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Personas Juridicas Prov');
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders'=>[
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => [
                    'argb' => 'FFA0A0A0',
                ],
                'endColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ],
        ];
        $styleArray1 = [
//            'quotePrefix'    => true,
            'NumberFormat'=>[
                'FormatCode'=>'#'
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ];
        $list = [];

        $sheet->getCell('A1')->setValue('NuLicencia');
        $sheet->getCell('B1')->setValue('Entidad');
        $sheet->getCell('C1')->setValue('Extension');
        $sheet->getCell('D1')->setValue('Rama');
        $sheet->getCell('E1')->setValue('Estado');
        $sheet->getCell('F1')->setValue('causadecancel');
        $sheet->getCell('G1')->setValue('causadesusp');
        $sheet->getCell('H1')->setValue('F. Solicitud');
        $sheet->getCell('I1')->setValue('Presentada');
        $sheet->getCell('J1')->setValue('Aprobada');
        $sheet->getCell('K1')->setValue('F. de Aprob');
        $sheet->getCell('L1')->setValue('F. de Emision');
        $sheet->getCell('M1')->setValue('F. de Entrega');
        $sheet->getCell('N1')->setValue('Limitacion');
        $sheet->getCell('O1')->setValue('Tpo Medio Amp.');
        $sheet->getCell('P1')->setValue('F de Cancel');
        $sheet->getCell('Q1')->setValue('F de Renov');
        $sheet->getCell('R1')->setValue('Media Tarifa');
        $sheet->getCell('S1')->setValue('Folio');
        $sheet->getCell('T1')->setValue('Aprob Inicial');
        $sheet->getCell('U1')->setValue('Dictamen');
        $sheet->getCell('V1')->setValue('Causa de Negacion');
        $sheet->getCell('W1')->setValue('Prorrogado en dias');
        $sheet->getCell('X1')->setValue('F destrucción');
        $sheet->getCell('Y1')->setValue('Duplicado');
        $sheet->getCell('Z1')->setValue('F impresion');
        $sheet->getCell('AA1')->setValue('F suspension');
        $sheet->getCell('AB1')->setValue('Importe');
        $sheet->getCell('AC1')->setValue('Tipo');
        $sheet->getCell('AD1')->setValue('Servicio');
        $sheet->getCell('AE1')->setValue('ServicioAmparado');

        $sheet->getStyle('A1:AE1')->applyFromArray($styleArray);

        $sheet->fromArray($resumen,null, 'A2', true);

        foreach ($sheet->getColumnIterator() as $column) {
            $sheet
                ->getColumnDimension($column->getColumnIndex())
                ->setAutoSize(true);
            $sheet->setAutoFilter('A1:K1');

        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $sheet->removeAutoFilter();
        // Crear archivo temporal en el sistema
        $fileName = 'Personas Juridicas prov.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);
        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
    /**
     * @Route("/exportmyprov", name="lotjuridicas_export_myprov")
     */
    public function exportmyprov(){
        $myprov= $this->getUser()->getMunicipio()->getProvinciaid();
        return $this->exportListado($myprov);
    }

    /**
     * @Route("/{id}", name="lotjuridicas_show", methods={"GET"})
     */
    public function show(Lotjuridicas $lotjuridica): Response
    {

        return $this->render('lotjuridicas/show.html.twig', [
            'lotjuridica' => $lotjuridica,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="lotjuridicas_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Lotjuridicas $lotjuridica,TipotramiteRepository $tt, EstadoTramiteRepository $et): Response
    {
        if($lotjuridica->getFimpresion() && !$this->isGranted('ROLE_ADMIN')){
            $this->addFlash('error','Solo se pueden editar las lots que no se hayan impreso aun');
            return $this->redirectToRoute('lotjuridicas_show',['id'=>$lotjuridica->getId()]);
        }
        $em= $this->getDoctrine()->getManager();
        $tramite = new Tramite();
        $tramite->setUsuario($this->getUser())
            ->setTipotramite($tt->find(1))
            ->setLot($lotjuridica)
            ->setEstado($et->find(1))
            ->setFecha(new \DateTime());
        try{
            $this->denyAccessUnlessGranted('LOT_EDIT', $tramite,"Usted no tiene permiso para editar una lot perteneciente a un municipio distinto al suyo");
        }catch (AccessDeniedException $exception){
           $this->addFlash('error',$exception->getMessage());
           return $this->redirectToRoute('lotjuridicas_show',['id'=>$lotjuridica->getId()]);
        }

        $form = $this->createForm(LotjuridicasType::class, $lotjuridica);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tramite->setEstado($et->find(3));
            $em->persist($tramite);
            $lotjuridica->setImporte(MainController::TarifaLot($this->getDoctrine()->getManager(),$lotjuridica)->getValor());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lotjuridicas_index');
        }

        return $this->render('lotjuridicas/edit.html.twig', [
            'lotjuridica' => $lotjuridica,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}/aprobacion",name="lotjuridicas_aprobacion")
     */
    public function aprobacion(Request $request,Lotjuridicas $lotjuridicas,TipotramiteRepository $tt, EstadoTramiteRepository $et): Response{
        $em= $this->getDoctrine()->getManager();
        $tramite = new Tramite();
        $tramite->setUsuario($this->getUser())
            ->setTipotramite($tt->find(4))
            ->setLot($lotjuridicas)
            ->setEstado($et->find(1))
            ->setFecha(new \DateTime());
        try{

            $aprueba=$lotjuridicas->getIdaprueba();
            if($aprueba){
              $aprueba = $aprueba->getNombreApellidos();
            }
            $om =  $lotjuridicas->getIdentidad()->getIdmunicipio()->getOficinaMcpals()->first();
            $this->denyAccessUnlessGranted('LOT_APROV', $tramite,"Usted no tiene permiso para Aprobar/Denegar esta lot. Solo $aprueba puede realizar esa accion definido en la $om.");
        }catch (AccessDeniedException $exception){
            $this->addFlash('error',$exception->getMessage());
            return $this->redirectToRoute('lotjuridicas_show',['id'=>$lotjuridicas->getId()]);
        }
        $form = $this->createForm(LotjuridicasType::class, $lotjuridicas);
        $form->handleRequest($request);
        if($request->getMethod()=="POST"){
            $lotjuridicas->setFechaaprobacion( \DateTime::createFromFormat('d/m/Y',$request->get("fechaaprob","today")));
            if($request->get("aprobacion","aprob")=="aprob"){
                $lotjuridicas->setAprobada(true);
                $lotjuridicas->setIdestado($em->getRepository(EstadoLot::class)->find(11));
                $this->addFlash('success', "La lot $lotjuridicas ahora se encuentra Pendiente de Impresion! ");
            }
            else{
                $lotjuridicas->setAprobada(false);
                $lotjuridicas->setCNegacion($request->get('cnegacion','denegada'));
                $lotjuridicas->setIdestado($em->getRepository(EstadoLot::class)->find(2));
                $this->addFlash('error', "La lot $lotjuridicas ahora se encuentra Denegada ");
            }
            $tramite->setEstado($et->find(3));
            $em->persist($tramite);
            $em->persist($lotjuridicas);
            $em->flush();
            if ($lotjuridicas->getIdestado()->getId()==11)
                return $this->redirectToRoute('lotjuridicas_ptesimpresion');
            return $this->redirectToRoute('lotjuridicas_pteAprob');
        }
        return $this->render("lotjuridicas/aprobacion.html.twig",[
            'lot'=>$lotjuridicas,
            'form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/{id}/entregar", name="lotjuridicas_entregar")
     */
    public function entregar(Request $request,Lotjuridicas $lotjuridicas,TipotramiteRepository $tt, EstadoTramiteRepository $et):Response{
        $em= $this->getDoctrine()->getManager();
        $tramite = new Tramite();
        $tramite->setUsuario($this->getUser())
            ->setTipotramite($tt->find(6))
            ->setLot($lotjuridicas)
            ->setEstado($et->find(1))
            ->setFecha(new \DateTime());
        try{
            $this->denyAccessUnlessGranted('LOT_ENTREGA', $tramite,"Usted no tiene permiso para entregar esta lot");
        }catch (AccessDeniedException $exception){
            $this->addFlash('error',$exception->getMessage());
            return $this->redirectToRoute('lotjuridicas_show',['id'=>$lotjuridicas->getId()]);
        }

        $form = $this->createForm(LotjuridicasType::class, $lotjuridicas);
        $form->handleRequest($request);
        if($request->getMethod()=="POST"){
            if($lotjuridicas->getIdestado()->getId()==3){
                $lotjuridicas->setFechaentrega(\DateTime::createFromFormat('d/m/Y',$request->get("fechaentrega","today")));
                $lotjuridicas->setIdestado($em->getRepository(EstadoLot::class)->find(5));
                $lotjuridicas->setImporte(MainController::TarifaLot($em,$lotjuridicas)->getValor());
                $tramite->setEstado($et->find(3));
                $em->persist($tramite);
                $em->persist($lotjuridicas);
                $em->flush();
                $this->addFlash('success', "La lot $lotjuridicas ahora se encuentra Vigente! ");
                return $this->redirectToRoute('lotjuridicas_ptesentrega');
            }
            else{
                $this->addFlash('error', "La lot $lotjuridicas No se encuentra pendiente de entrega ");
            }
        }
        return $this->render("lotjuridicas/entrega.html.twig",[
            'lot'=>$lotjuridicas,
            'form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/{id}/imprimir/{folio}", name="lotjuridicas_imprimir")
     */
    public function imprimir(Request $request, Lotjuridicas $lotjuridicas,string $folio,TipotramiteRepository $tt, EstadoTramiteRepository $et){
        $em= $this->getDoctrine()->getManager();
        $tramite = new Tramite();
        $tramite->setUsuario($this->getUser())
            ->setTipotramite($tt->find(5))
            ->setLot($lotjuridicas)
            ->setEstado($et->find(1))
            ->setFecha(new \DateTime());
        try{
            $this->denyAccessUnlessGranted('LOT_PRINT', $tramite,"Solo los usuarios de la om donde radica la pj pueden imprimir esta lot");
        }catch (AccessDeniedException $exception){
            $this->addFlash('error',$exception->getMessage());
            return $this->redirectToRoute('lotjuridicas_show',['id'=>$lotjuridicas->getId()]);
        }

        if($folio !== null && $lotjuridicas->getIdestado()->getId()==11){
            //revisr que el folio no se haya asignado a otra lot
            $existe= $em->getRepository(Lotjuridicas::class)->findOneBy(['numfolio'=>$folio]);
            if($existe===null || $folio == $lotjuridicas->getNumfolio()){
                $lotjuridicas->setNumfolio($folio);
                $lotjuridicas->setFimpresion(new \DateTime('today'));

                $em->persist($lotjuridicas);
                $em->flush();

                $comp = $lotjuridicas;
                // Configure Dompdf according to your needs
                $pdfOptions = new Options();
                $pdfOptions->set('defaultFont', 'serif');

                // Instantiate Dompdf with our options
                $dompdf = new Dompdf($pdfOptions);
                // return $this->render('lotjuridicas/lotpdf.html.twig', [
                //     'title' => "Licencia de operacion de transporte $comp",'comp'=>$comp,'folio'=>$folio
                // ]);
                // Retrieve the HTML generated in our twig file
                $html = $this->renderView('lotjuridicas/lotpdf.html.twig', [
                    'title' => "Licencia de operacion de transporte $comp",'comp'=>$comp,'folio'=>$folio
                ]);

                // Load HTML to Dompdf
                $dompdf->loadHtml($html);
                $filename=$comp->__toString();
                // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
                //$customPaper= array(0,0,289,408.30);
                $dompdf->setPaper('letter');


                // Render the HTML as PDF
                $dompdf->render();

                // Output the generated PDF to Browser (force download)
                $dompdf->stream($filename, [
                    "Attachment" => false
                ]);
            }
            else{
                $this->addFlash('error',"No se puede asignar el mismo folio a mas de una lot");
                return $this->redirectToRoute('lotjuridicas_index');
            }

        }
        else
        return $this->render("lotjuridicas/imprimir.html.twig");
    }
    /**
     * @Route("/{id}/marcarcomoimpresa", name="lotjuridicas_marcarcomoimpresa")
     */
    public function marcarComoImpresa(Lotjuridicas $lotjuridicas,Request $request,TipotramiteRepository $tt, EstadoTramiteRepository $et){
        $em= $this->getDoctrine()->getManager();
        $tramite = new Tramite();
        $tramite->setUsuario($this->getUser())
            ->setTipotramite($tt->find(5))
            ->setLot($lotjuridicas)
            ->setEstado($et->find(1))
            ->setFecha(new \DateTime());
        try{
            $this->denyAccessUnlessGranted('LOT_PRINT', $tramite,"Usted no tiene permiso para realizar esta accion. Asegurese de que pertenece a la OM del municipio donde radica la pj.");
        }catch (AccessDeniedException $exception){
            $this->addFlash('error',$exception->getMessage());
            return $this->redirectToRoute('lotjuridicas_show',['id'=>$lotjuridicas->getId()]);
        }
        $em= $this->getDoctrine()->getManager();
        if($lotjuridicas->getIdestado()->getId()==11 and $lotjuridicas->getNumfolio()!== null){
            $lotjuridicas->setIdestado($em->getRepository(EstadoLot::class)->find(3));
            $tramite->setEstado($et->find(3));
            $em->persist($tramite);
            $em->persist($lotjuridicas);
            $em->flush();
            $this->addFlash('success', "La lot $lotjuridicas ahora se encuentra Pendiente de Entrega! ");
        }
        else{
            $this->addFlash('error', "La lot $lotjuridicas no tiene un folio valido asignado o no se ha imprimido aun");
            $dondeEstaba = $request->server->get('HTTP_REFERER');
            return new RedirectResponse($dondeEstaba, 302);
        }
        return $this->redirectToRoute('lotjuridicas_ptesentrega');
    }
    /**
     * @Route("/{id}/cancelar",name="lotjuridicas_cancelar")
     */
    public function cancelar(Request $request, Lotjuridicas $lotjuridicas,TipotramiteRepository $tt, EstadoTramiteRepository $et){

        $em= $this->getDoctrine()->getManager();
        $tramite = new Tramite();
        $tramite->setUsuario($this->getUser())
            ->setTipotramite($tt->find(9))
            ->setLot($lotjuridicas)
            ->setEstado($et->find(1))
            ->setFecha(new \DateTime());
        try{
            $this->denyAccessUnlessGranted('LOT_PRINT', $tramite,"Usted no tiene permiso para realizar esta accion. Asegurese de que pertenece a la OM del municipio donde radica la pj.");
        }catch (AccessDeniedException $exception){
            $this->addFlash('error',$exception->getMessage());
            return $this->redirectToRoute('lotjuridicas_show',['id'=>$lotjuridicas->getId()]);
        }
        $form = $this->createForm(LotjuridicasType::class, $lotjuridicas);
        $form->handleRequest($request);
        $causas= $em->getRepository(CausaCancelacionLot::class)->findAll();
        if($request->getMethod()=="POST"){
            $estado= $lotjuridicas->getIdestado()->getId();dump($request);
            if($estado >= 4 && $estado <= 6 ){
                $fecha= $request->get('fechacancel');
                $causac= $request->get('causacancel');
                if ($fecha && $causac){
                    $lotjuridicas->setFechadecancelacion(\DateTime::createFromFormat('d/m/Y',$fecha));
                    $lotjuridicas->setCausadecancelacion($em->getRepository(CausaCancelacionLot::class)->find($causac));
                    $lotjuridicas->setIdestado($em->getRepository(EstadoLot::class)->find(8));
                    $tramite->setEstado($et->find(3));
                    $em->persist($tramite);
                    $em->persist($lotjuridicas);
                    $em->flush();
                    $this->addFlash('success', "La lot $lotjuridicas ahora se encuentra Cancelada! ");
                    return $this->redirectToRoute('lotjuridicas_index');
                }
            }
        }
        return $this->render('lotjuridicas/cancelar.html.twig',[
            'lot'=>$lotjuridicas,
            'form'=>$form->createView(),
            'causas'=>$causas,
            ]);
    }
    /**
     * @Route("/{id}/suspender", name="lotjuridicas_suspender")
     */
    public function suspenderLot(Request $request, Lotjuridicas $lotjuridicas,TipotramiteRepository $tt, EstadoTramiteRepository $et){
        $em= $this->getDoctrine()->getManager();
        $tramite = new Tramite();
        $tramite->setUsuario($this->getUser())
            ->setTipotramite($tt->find(8))
            ->setLot($lotjuridicas)
            ->setEstado($et->find(1))
            ->setFecha(new \DateTime());
        try{
            $this->denyAccessUnlessGranted('LOT_PRINT', $tramite,"Usted no tiene permiso para realizar esta accion. Asegurese de que pertenece a la OM del municipio donde radica la pj.");
        }catch (AccessDeniedException $exception){
            $this->addFlash('error',$exception->getMessage());
            return $this->redirectToRoute('lotjuridicas_show',['id'=>$lotjuridicas->getId()]);
        }
        $form = $this->createForm(LotjuridicasType::class, $lotjuridicas);
        $form->handleRequest($request);
        $causas= $em->getRepository(CausaSuspensionLot::class)->findAll();
        if($request->getMethod()=="POST"){
            $estado= $lotjuridicas->getIdestado()->getId();
            if($estado ==5 ){
                $fecha= $request->get('fechasusp');
                $causac= $request->get('causasuspencion');
                if ($fecha && $causac){
                    $lotjuridicas->setFechaSuspension(\DateTime::createFromFormat('d/m/Y',$fecha));
                    $lotjuridicas->setCausaSuspension($em->getRepository(CausaSuspensionLot::class)->find($causac));
                    $lotjuridicas->setIdestado($em->getRepository(EstadoLot::class)->find(6));
                    $tramite->setEstado($et->find(3));
                    $em->persist($tramite);
                    $em->persist($lotjuridicas);
                    $em->flush();
                    $this->addFlash('success', "La lot $lotjuridicas ahora se encuentra Suspendida! ");
                    return $this->redirectToRoute('lotjuridicas_index');
                }
            }
        }
        return $this->render('lotjuridicas/suspender.html.twig',[
            'lot'=>$lotjuridicas,
            'form'=>$form->createView(),
            'causas'=>$causas,
        ]);
    }
    /**
     * @Route("/{id}/renovar", name="lotjuridicas_renovar")
     */
    public function renovarLot(Request $request, Lotjuridicas $lotjuridicas,TipotramiteRepository $tt, EstadoTramiteRepository $et){
        $em= $this->getDoctrine()->getManager();
        $tramite = new Tramite();
        $tramite->setUsuario($this->getUser())
            ->setTipotramite($tt->find(3))
            ->setLot($lotjuridicas)
            ->setEstado($et->find(1))
            ->setFecha(new \DateTime());
        try{
            $this->denyAccessUnlessGranted('LOT_PRINT', $tramite,"Usted no tiene permiso para realizar esta accion. Asegurese de que pertenece a la OM del municipio donde radica la pj.");
        }catch (AccessDeniedException $exception){
            $this->addFlash('error',$exception->getMessage());
            return $this->redirectToRoute('lotjuridicas_show',['id'=>$lotjuridicas->getId()]);
        }
        $lotjuridicas->renovar($em->getRepository(EstadoLot::class)->find(1));
        $form = $this->createForm(LotjuridicasType::class, $lotjuridicas);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $tramite->setEstado($et->find(3));
            $em->persist($tramite);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "La lot $lotjuridicas ahora se encuentra Pendiente de Aprobacion! ");
            return $this->redirectToRoute('lotjuridicas_index');
        }
        return $this->render('lotjuridicas/renovar.html.twig',[
            'lotjuridica'=>$lotjuridicas,
            'form'=>$form->createView(),
        ]);
    }
    /**
     * @Route("/{id}/duplicado", name="lotjuridicas_duplicado")
     */
    public function duplicado(Lotjuridicas $lotjuridicas, Request $request,TipotramiteRepository $tt, EstadoTramiteRepository $et){
        $em= $this->getDoctrine()->getManager();
        $tramite = new Tramite();
        $tramite->setUsuario($this->getUser())
            ->setTipotramite($tt->find(7))
            ->setLot($lotjuridicas)
            ->setEstado($et->find(1))
            ->setFecha(new \DateTime())

        ;
        try{
            $this->denyAccessUnlessGranted('LOT_DUPLICADO', $tramite,"Usted no tiene permiso para realizar un duplicado de esta lot");
        }catch (AccessDeniedException $exception){
            $this->addFlash('error',$exception->getMessage());
            return $this->redirectToRoute('lotjuridicas_show',['id'=>$lotjuridicas->getId()]);
        }

        $lotjuridicas->Duplicar($em->getRepository(EstadoLot::class)->find(11));
        $lotjuridicas->setIdaprueba($this->DetAprobacion($lotjuridicas));
        $form = $this->createForm(LotjuridicasType::class, $lotjuridicas);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $tramite->setEstado($et->find(3));
            $em->persist($tramite);
            $lotjuridicas->setImporte(MainController::Tarifa($em,'Comprobantes y Duplicados',$lotjuridicas->getIdextension())->getValor());
            $em->persist($lotjuridicas);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "El duplicado de la lot $lotjuridicas ahora se encuentra Pendiente de Impresion! ");
            return $this->redirectToRoute('lotjuridicas_ptesimpresion');
        }
        return $this->render('lotjuridicas/duplicar.html.twig',[
            'lotjuridica'=>$lotjuridicas,
            'form'=>$form->createView(),
        ]);

    }

    /**
     * @Route("/{id}/nuevotramite", name="lotjuridicas_newtramit")
     */
    public function nuevoTramite(Request $request,Lotjuridicas $lotjuridicas): Response{

        $form = $this->createForm(LotjuridicasType::class, $lotjuridicas);
        $form->handleRequest($request);
        return $this->render("lotjuridicas/index.html.twig");
    }

    /**
     * @Route("/{id}", name="lotjuridicas_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Lotjuridicas $lotjuridica,TipotramiteRepository $tt, EstadoTramiteRepository $et): Response
    {
        $em= $this->getDoctrine()->getManager();
//        $tramite = new Tramite();
//        $tramite->setUsuario($this->getUser())
//            ->setTipotramite($tt->find(8))
////            ->setLot()
//            ->setEstado($et->find(1))
//            ->setFecha(new \DateTime());
//        try{
//            $this->denyAccessUnlessGranted('LOT_DELETE', $tramite,"Usted no tiene permiso para realizar esta accion. Asegurese de que pertenece a la OM del municipio donde radica la pj.");
//        }catch (AccessDeniedException $exception){
//            $this->addFlash('error',$exception->getMessage());
//            return $this->redirectToRoute('lotjuridicas_show',['id'=>$lotjuridica->getId()]);
//        }
        if ($this->isCsrfTokenValid('delete'.$lotjuridica->getId(), $request->request->get('_token'))) {

            $basificaciones = $lotjuridica->getBasificaciones();
            if(sizeof($basificaciones)){
                $this->addFlash('error','La lot tiene basificaciones declaradas. Elimine las mismas para poder eliminar esta lot.');
                return $this->redirectToRoute('lotjuridicas_show',['id'=>$lotjuridica->getId()]);
            }
            $estab = $lotjuridica->getInstalaciones();
            if(sizeof($estab)){
                $this->addFlash('error','La lot tiene Instalaciones declaradas. Elimine las mismas para poder eliminar esta lot.');
                return $this->redirectToRoute('lotjuridicas_show',['id'=>$lotjuridica->getId()]);
            }
            $em->remove($lotjuridica);
            $em->flush();
        }
        return $this->redirectToRoute('lotjuridicas_index');
    }
    private function DetAprobacion(Lotjuridicas $lot): ?User
    {
        $user = $this->getUser();
        $dp= $user->getDireccionProvincial();
        $om= $this->getUser()->getOficinaMcpal();
        if($dp){
           return $dp->getFirmalot();
        }
        if($om){
            return  $om->getFirmalot() ?  $om->getFirmalot() : $om->getDireccionProvincial()->getFirmalot();
        }
//        $firmalot = $user->getOficinaMcpal()->getFirmalot();
//        if($firmalot==null){
//            $firmalot = $this->getUser()->getOficinaMcpal()->getDireccionProvincial()->getFirmalot();
//        }
//        return $firmalot;
        return null;
    }

}
