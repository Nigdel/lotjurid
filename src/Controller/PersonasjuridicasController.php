<?php

namespace App\Controller;

use App\Entity\Municipios;
use App\Entity\Personasjuridicas;
use App\Entity\Provincias;
use App\Entity\Tramite;
use App\Form\PersonasjuridicasType;
use App\Repository\EstadoTramiteRepository;
use App\Repository\PersonasjuridicasRepository;
use App\Repository\TipotramiteRepository;
use App\Repository\TramiteRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/personasjuridicas")
 */
class PersonasjuridicasController extends AbstractController
{
    /**
     * @Route("/", name="personasjuridicas_index", methods={"GET"})
     */
    public function index(PersonasjuridicasRepository $repository, Request $request, PaginatorInterface $paginator ): Response
    {
        $queryBuilder = null;
        $exp= null;
        $nombre=null;
        $search = $request->query->get('exp');
        if ($search) {
            $queryBuilder = $repository->searchExpediente($search);
            $exp= $search;
        } else{
            $search = $request->query->get('nombre');
            if ($search) {
                $queryBuilder = $repository->searchNombre($search);
                $nombre= $search;
            }else{
                $queryBuilder = $repository->getWithSearchQueryBuilder();
            }
        }
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            15/*limit per page*/
        );

        return $this->render('personasjuridicas/index.html.twig', [
            'pagination' => $pagination,
            'exp'=>$exp,
            'nombre'=>$nombre,
        ]);
    }

    /**
     * @Route("/search", name="personasjuridicas_search" )
     */
    public function buscar(Request $request){
        return $this->render('personasjuridicas/buscar.html.twig');
    }

    /**
     * @Route("/new", name="personasjuridicas_new", methods={"GET","POST"})
     */
    public function new(Request $request,TipotramiteRepository $tt, EstadoTramiteRepository $et): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user= $this->getUser();
        $tramite= new Tramite();
        $tramite->setTipotramite($tt->find(13));
        $tramite->setUsuario($user);
        $tramite->setFecha( new \DateTime('now'));
        $tramite->setEstado($et->find(1));

//        $mcpio= $user->getMunicipio();
        $mcpios=$this->getDoctrine()->getManager()->getRepository(Municipios::class)->findBy(['provinciaid'=>$user->getMunicipio()->getProvinciaId()->getId()]);
        $personasjuridica = new Personasjuridicas();

        $form = $this->createForm(PersonasjuridicasType::class, $personasjuridica,['municipios'=>$mcpios]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $tramite->setObservaciones("add pj: $personasjuridica");
            $tramite->setEstado($et->find(3));
            $tramite->setPj($personasjuridica);
            $entityManager->persist($tramite);
            $entityManager->persist($personasjuridica);
            try{
                $entityManager->flush();
            }
           catch (UniqueConstraintViolationException $exception ){
                $this->addFlash('error',"Ya existe una persona juridica con ese numero de expediente. No se creó la pj");
               return $this->redirectToRoute('personasjuridicas_show',['id'=>$personasjuridica->getId()]);
           }
            $this->addFlash('success',"Se ha declarado la Persona Juridica correctamente.");
            return $this->redirectToRoute('personasjuridicas_show',['id'=>$personasjuridica->getId()]);
        }

        return $this->render('personasjuridicas/new.html.twig', [
            'personasjuridica' => $personasjuridica,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new/{lot}", name="personasjuridicas_newbylot", methods={"GET","POST"})
     */
    public function newbyLot(Request $request,string $lot): Response
    {
        //dump($request->query->get('lot'," "));
        $user= $this->getUser();
        $mcpio= $user->getMunicipio();
        $personasjuridica = new Personasjuridicas($mcpio);
        $personasjuridica->setId($lot);

        $form = $this->createForm(PersonasjuridicasType::class, $personasjuridica);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($personasjuridica);
            $entityManager->flush();

            return $this->redirectToRoute('lotjuridicas_newbypj',['pj'=>$personasjuridica->getId()]);
        }

        return $this->render('personasjuridicas/newbyLot.html.twig', [
            'personasjuridica' => $personasjuridica,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/exportmyprov", name="personasjuridicas_export_myprov")
     */
    public function exportmyprov(){
        $myprov= $this->getUser()->getMunicipio()->getProvinciaid();
        return $this->exportListado($myprov);
    }

    /**
     * @Route("/export/{prov}", name="personasjuridicas_export_prov")
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportListado(Provincias $prov){
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT m.Municipio AS idmunicipio_12, p0_.CodReeup AS CodReeup_1, p0_.NomEntidad AS NomEntidad_2, p0_.Telefono AS Telefono_3, p0_.Email AS Email_4, p0_.Direccion AS Direccion_5, p0_.Actividad AS Actividad_6, p0_.Rama AS Rama_7, p0_.SubRama AS SubRama_8, p0_.NoContribuyente AS NoContribuyente_9, p0_.idorga AS idorga_11, te.tipo as `tipoEmpresa` FROM personasjuridicas p0_ INNER JOIN municipios m on(p0_.idmunicipio = m.ID) INNER JOIN tipo_empresa te on(p0_.tipoEmpresa = te.id) where m.ProvinciaID=:prov;";
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

        $sheet->getCell('B1')->setValue('CodReeup');
        $sheet->getCell('C1')->setValue('Entidad');
        $sheet->getCell('D1')->setValue('Telefono');
        $sheet->getCell('E1')->setValue('Mail');
        $sheet->getCell('F1')->setValue('Direccion');
        $sheet->getCell('G1')->setValue('Actividad');
        $sheet->getCell('H1')->setValue('Rama');
        $sheet->getCell('I1')->setValue('SubRama');
        $sheet->getCell('J1')->setValue('No. Contrib');
        $sheet->getCell('K1')->setValue('Organismo');
        $sheet->getCell('L1')->setValue('Tipo de Empresa');
        $sheet->getCell('A1')->setValue('Municipio');
        $sheet->getStyle('A1:L1')->applyFromArray($styleArray);

        $sheet->fromArray($resumen,null, 'A2', true);

        foreach ($sheet->getColumnIterator() as $column) {
            $sheet
                ->getColumnDimension($column->getColumnIndex())
                ->setAutoSize(true);
            $sheet->setAutoFilter('A1:L1');

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
     * @Route("/export/", name="personasjuridicas_export")
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportListadoNac(){
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT  p.Provincias as provincias_13, m.Municipio AS idmunicipio_12, p0_.CodReeup AS CodReeup_1, p0_.NomEntidad AS NomEntidad_2, p0_.Telefono AS Telefono_3, p0_.Email AS Email_4, p0_.Direccion AS Direccion_5, p0_.Actividad AS Actividad_6, p0_.Rama AS Rama_7, p0_.SubRama AS SubRama_8, p0_.NoContribuyente AS NoContribuyente_9, p0_.idorga AS idorga_11, te.tipo as 'tipoEmpresa'  FROM personasjuridicas p0_ INNER JOIN municipios m on(p0_.idmunicipio = m.ID) INNER join provincias p on(m.ProvinciaID=p.ID) INNER JOIN tipo_empresa te on(p0_.tipoEmpresa = te.id);";
        $stmt = $db->prepare($sql);
        $prov = 1;
        $params = array('prov'=>$prov);
        $stmt->execute($params);
        $resumen= $stmt->fetchAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Personas Juridicas Nac');
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

        $sheet->getCell('C1')->setValue('CodReeup');
        $sheet->getCell('D1')->setValue('Entidad');
        $sheet->getCell('E1')->setValue('Telefono');
        $sheet->getCell('F1')->setValue('Mail');
        $sheet->getCell('G1')->setValue('Direccion');
        $sheet->getCell('H1')->setValue('Actividad');
        $sheet->getCell('I1')->setValue('Rama');
        $sheet->getCell('J1')->setValue('SubRama');
        $sheet->getCell('K1')->setValue('No. Contrib');
        $sheet->getCell('L1')->setValue('Organismo');
        $sheet->getCell('M1')->setValue('Tipo de Empresa');
        $sheet->getCell('B1')->setValue('Municipio');
        $sheet->getCell('A1')->setValue('Provincia');
        $sheet->getStyle('A1:M1')->applyFromArray($styleArray);

        $sheet->fromArray($resumen,null, 'A2', true);

        foreach ($sheet->getColumnIterator() as $column) {
            $sheet
                ->getColumnDimension($column->getColumnIndex())
                ->setAutoSize(true);
                $sheet->setAutoFilter('A1:M1');

        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $sheet->removeAutoFilter();
        // Crear archivo temporal en el sistema
        $fileName = 'Personas Juridicas Nac.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);
        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/{id}", name="personasjuridicas_show", methods={"GET"})
     */
    public function show(Personasjuridicas $personasjuridica): Response
    {
        $this->denyAccessUnlessGranted('ver', $personasjuridica);
        return $this->render('personasjuridicas/show.html.twig', [
            'personasjuridica' => $personasjuridica,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="personasjuridicas_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Personasjuridicas $personasjuridica, EstadoTramiteRepository $et, TipotramiteRepository $tt): Response
    {
        $em =$this->getDoctrine()->getManager();
        $tramite = new Tramite();
        $tramite->setUsuario($this->getUser())
            ->setTipotramite($tt->find(2))
            ->setEstado($et->find(1))
            ->setPj($personasjuridica)
            ->setFecha(new \DateTime());
        try{
            $this->denyAccessUnlessGranted('PJ_EDIT', $tramite,"Usted no puede editar una Persona Juridica perteneciente a un municipio de una provincia distinta a la suya");
        }catch (AccessDeniedException $exception){
            $this->addFlash('error',$exception->getMessage());
            return $this->redirectToRoute('personasjuridicas_show',['id'=>$personasjuridica->getId()]);
        }

        $mcpios=$this->getDoctrine()->getManager()->getRepository(Municipios::class)->findBy(['provinciaid'=>$this->getUser()->getMunicipio()->getProvinciaId()->getId()]);
        $form = $this->createForm(PersonasjuridicasType::class, $personasjuridica,['municipios'=>$mcpios]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tramite->setEstado($et->find(3));
            $em->persist($tramite);
            $em->flush();
            $this->addFlash('success',"Se ha editado correctamente la entidad $personasjuridica");
            return $this->redirectToRoute('personasjuridicas_show',['id'=>$personasjuridica->getId()]);
        }

        return $this->render('personasjuridicas/edit.html.twig', [
            'personasjuridica' => $personasjuridica,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="personasjuridicas_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Personasjuridicas $personasjuridica): Response
    {
        if ($this->isCsrfTokenValid('delete'.$personasjuridica->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $lots = $personasjuridica->getLot();
            if($lots){
                $this->addFlash('error',"La Persona Juridica tiene lots registradas. Para eliminar esta persona juridica elimine sus lots primeramente. No se realizará ninguna accion.");
                return $this->redirectToRoute('personasjuridicas_index');
            }
            $tramites =$entityManager->getRepository(Tramite::class)->findBy(['pj'=>$personasjuridica->getId()]);
            foreach ($tramites as $tramit){
                $entityManager->remove($tramit);
            }
            $entityManager->remove($personasjuridica);
            $entityManager->flush();
        }

        return $this->redirectToRoute('personasjuridicas_index');
    }


}
