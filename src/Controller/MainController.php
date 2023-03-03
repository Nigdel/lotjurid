<?php

namespace App\Controller;

use App\Entity\Basificacion;
use App\Entity\Comprobante;
use App\Entity\Compestab;
use App\Entity\DireccionProvincial;
use App\Entity\Establecimientos;
use App\Entity\Extension;
use App\Entity\Folio;
use App\Entity\Foliocomprobantes;
use App\Entity\Instalaciones;
use App\Entity\Lotjuridicas;
use App\Entity\MediosTrans;
use App\Entity\NombEstComp;
use App\Entity\Provincias;
use App\Entity\Tarifa;
use App\Entity\TipoServAuxCon;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\Persistence\ObjectManager;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;


//-------------------------------------------------------------------------------

//---------------------------------------------------------------------------------

class MainController extends AbstractController
{
    /**
     * @Route("/", name="portada")
     */
    public function index(){
        $user = $this->getUser();
        $prov= $user->getMunicipio()->getProvinciaid()->getId();
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT * FROM `lotsxestadoxprov` where `provinciaid`=:prov";
        $stmt = $db->prepare($sql);
        $params = array('prov'=>$prov);
        $stmt->execute($params);
        $datosEstLot=$stmt->fetchAll();

//        $medios = $this->getDoctrine()->getManager()->getRepository(MediosTrans::class)->findMediosbyMun($user->getMunicipio());
        $medios = $this->getDoctrine()->getManager()->getRepository(MediosTrans::class)->findMediosbyProv($user->getMunicipio()->getProvinciaid());

//        $aptos = $this->getDoctrine()->getManager()->getRepository(MediosTrans::class)->findMediosAptosbyMun($user->getMunicipio());
        $aptos = $this->getDoctrine()->getManager()->getRepository(MediosTrans::class)->findMediosAptosbyProv($user->getMunicipio()->getProvinciaid());

        $sql = "SELECT * FROM `compsxestadoxmcpio` where `provid`=:prov";
        $stmt = $db->prepare($sql);
        $params = array('prov'=>$user->getMunicipio()->getProvinciaid()->getId());
        $stmt->execute($params);
        $medioscomp=$stmt->fetchAll();

        $sql = "SELECT sum(`importe`) as 'importe',`concepto`,`provid`, count(*) as 'cant' FROM `ingresoshoygral` where `provid`=:prov GROUP by `concepto` ";
        $stmt = $db->prepare($sql);
        $params = array('prov'=>$user->getMunicipio()->getProvinciaid()->getId());
        $stmt->execute($params);
        $ingresos=$stmt->fetchAll();
        /**
        Prueba con web services
         */
//        $datos = file_get_contents('https://s3.amazonaws.com/dolartoday/data.json');
//        $url = $this->generateUrl('estadistica',[], UrlGeneratorInterface::ABSOLUTE_URL);
//        $datos = file_get_contents($url);
//        $array = json_decode($datos, true);
//        $valor = $array['USD']['dolartoday'];
//        dump($url);
//       dump($datos);
        /**
        Fin Prueba con web services
         */

       return $this->render("mainportada.html.twig",[
           'datosEstLot' => $datosEstLot,
           'alcance'=>'provincia',
           'alcancevalor' => $user->getMunicipio()->getProvinciaid(),
           'mediosT'=>$medios,
           'aptos' =>$aptos,
           'medioscomp'=>$medioscomp,
           'ingresos'=>$ingresos,
       ]);
//        return $this->render("usuario/base2.html.twig");
    }
    /**
     * @Route("/fillcomp", name="llenacomp")
     */
    public function index1()
    {
       $medios = $this->getDoctrine()->getRepository(MediosTrans::class)->findAll();
       $lots = $this->getDoctrine()->getRepository(Lotjuridicas::class)->findAll();
       $compViejos = $this->getDoctrine()->getRepository(Foliocomprobantes::class)->findAll();
       $extensiones = $this->getDoctrine()->getRepository(Extension::class)->findAll();
       $estados = $this->getDoctrine()->getRepository(NombEstComp::class)->findAll();
       $em = $this->getDoctrine()->getManager();

       //Llenar los folios con los valores heredados de los comp viejos
//       foreach ($compViejos as $comp1){
//           $folioTemp= new Folio($comp1->getFolio());
//           $em->persist($folioTemp);
//       }
//       $em->flush();

      //  $this->LlenarFolios($compViejos);
       $folios = $this->getDoctrine()->getRepository(Folio::class)->findAll();
       foreach ($compViejos as $comp){
           $chapa= $comp->getChapa();
           $medioAut= NULL;
           foreach ($medios as $medio){
               if($medio->getNombre() == $chapa && $medio->getRama()->getId()==1)
               {
                   $medioAut = $medio;
               }
           }
           if ($medioAut !== NULL){
              $nuevaLot = NULL;
              foreach ($lots as $licencia){
                  if ($comp->getLicencia() == $licencia->getId()){
                      $nuevaLot=  $licencia;
                  }
              }
            if($nuevaLot !==NULL){
                $folioViejo = $comp->getFolio();
//                foreach ($folios as $fol){
//                    if ($fol->getValor() == $folioViejo){
                        $nuevocomp = new Comprobante($medioAut);
                        $nuevocomp->setFolio($folioViejo);
                        $nuevocomp->setLot($nuevaLot);
                        foreach ($extensiones as $ext){
                            if($comp->getExtension()== $ext->getId()){
                                $nuevocomp->setExtension($ext);
                            }
                        }
                        $nuevocomp->setFemitido($comp->getFechaemitido());
                        $nuevocomp->setDuplicado($comp->getDuplicado());
                        $nuevocomp->setFcancel($comp->getFechadecancelacion())
                            ->setFentrega($comp->getFechaentrega())
                            ->setImporte($comp->getImporte())
                            ->setSincostp($comp->getSincosto())
                            ->setFimpreso($comp->getFechaimpresion())
                            ->setEstadoComp($this->DetEstado($comp,$estados));
                        $medioAut->addComprobante($nuevocomp);
                        $em->persist($nuevocomp);
//                    }
//                }
            }
           }
       }


      $em->flush();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
    * @Route("/test")
    */
    public function Testear(){
//        $medio = $this->getDoctrine()->getRepository(MediosTrans::class)->find(1733);
//        $comps= $medio->getComprobantes()->toArray();
//        usort($comps,function (Comprobante $a,Comprobante $b){
//            if ($a->getFemitido() == $b->getFemitido()) {
//                return 0;
//            }
//            return ($a->getFemitido() < $b->getFemitido()) ? -1 : 1;
//
//        });
//        foreach ($comps as $comp){
//            echo $comp."<br/>" ;
//        }
//        $lot = $this->getDoctrine()->getRepository(Lotjuridicas::class)->find("J-0359-31");
//
//        foreach ($lot->getComprobantes() as $comp){
//            echo $comp."<br/>" ;
//       }
         $lot = $this->getDoctrine()->getRepository(Lotjuridicas::class)->find("J-0359-31");
//        foreach ($lot->getBasificaciones() as $comp){
//            //$meds= $comp->getMedios()->toArray();
//            echo $comp."( medios: ".count($comp->getMedios()).")<br/>" ;
//        }

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController','lot'=>$lot
        ]);
    }

    public function LlenarFolios( $compViejos) : array
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($compViejos as $comps){
            $folioTemp= new Folio($comps->getFolio());
            $em->persist($folioTemp);
           // echo $folioTemp->getId();
        }
        $em->flush();
    }

    public function DetEstado(Foliocomprobantes $compViejo,$estados): ?NombEstComp
    {
        switch ($compViejo->getEstadomodelolot()){
            case 1: return null;
            case 2: return $estados[0];
            case 3: return $estados[1];
            case 4: return $estados[3];
            case 5: return $estados[5];
        }
    }

    /**
     * @Route("/pdf")
     */
    public function pdfRender(){
        $comp = $this->getDoctrine()->getRepository(Comprobante::class)->find(1);
// Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('dompdf/mypdf.html.twig', [
            'title' => "Comprobante Automotor",'comp' => $comp
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        $filename=$comp->getFolio()->getValor();
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $customPaper= array(0,0,289,408.30);
        $dompdf->setPaper($customPaper, 'landscape');


        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream($filename, [
            "Attachment" => false
        ]);
    }



    /**
     * @Route("/rep/Basif")
     */
    public function RepBasifEnMedios(){
        $basif = $this->getDoctrine()->getRepository(Basificacion::class)->findAll();
        $medios = $this->getDoctrine()->getRepository(MediosTrans::class)->findAll();
        foreach ($medios as $med){
            $base=null;
            foreach ($basif as $bas){
                if($bas->getIdlbasiam()=== $med->getBasificacion()){
                    $med->setBasificacionObj($bas);
                    $this->getDoctrine()->getManager()->persist($med);
                }
            }
        }
        $this->getDoctrine()->getManager()->flush();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/importinstalaciones", name="importarInstalaciones")
     */
    public function importInstalaciones(){
        $em=$this->getDoctrine()->getManager();
        $lots = $this->getDoctrine()->getRepository(Lotjuridicas::class)->findAll();
        /* la tabla establecimientos es donde voy a volcar la informacion de las bds de las prov relativas a los estab*/
        $estab = $this->getDoctrine()->getRepository(Establecimientos::class)->findAll();

        $nuevas = array();
        foreach ($estab as $est){                       //por cada estab en esa tabla
            foreach ($lots as $lot){                    //por cada lot ya registrada
                if ($est->getLicencia()===$lot->getId()){ //si la lot del estab. coincide con la lot del loop registro la instal.
                    $insta = new Instalaciones();
                    $insta->setLot($lot);
                    $insta->setNombre($est->getNombreestab());
                    $insta->setMunicipio($est->getMunicipio());
                    $insta->setAseguramiento($est->isAseguramiento());
                    $insta->setDireccion($est->getLugardeubicacion());
                    $insta->setTipoAuxCon($est->getTpoauxcon());

                    /*Ojo aqui se modifican los servicios*/
                    $insta->setServAuxCon($this->reinterpretServEstab($est->getTposerv()));

                    /* Se llenan los datos del comprobantte para la instalacion */
                    $compestab = new Compestab();
                    $compestab->setId($est->getNumcomp());
                    $compestab->setFolio($est->getNumcomp());
                    $compestab->setFemitido($est->getFechadeemision());
                    $compestab->setFentrega($est->getFechaentrega());
                    $compestab->setFimpreso($est->getFechaImpresion());
                    $compestab->setEstadoComp($em->getRepository(NombEstComp::class)->find(4));
                    $compestab->setImporte($est->getImporte());
                    $compestab->setDuplicado($est->isDuplicado());
                    $compestab->setSincostp($est->getSinCosto());
                    $compestab->setFcancel($est->getFechaDeCancelacion());
                    $compestab->setCCancel($est->getCausadeCancelacion());
                    // se asigna el comprobante a la instalacion
                    $insta->addComprobante($compestab);
                    $nuevas[]= $insta;
                    //marcamos para guardar
                    $em->persist($compestab);
                    $em->persist($insta);
                }
            }
        }
        $em->flush();
        return $this->render('mainportada.html.twig',['nuevas'=>$nuevas]);
    }
    public function reinterpretServEstab(TipoServAuxCon $oldone ) : ?TipoServAuxCon{
        $ServRep=$this->getDoctrine()->getManager()->getRepository(TipoServAuxCon::class);
        switch ($oldone->getId()){
            case 1:{ return $ServRep->find(24) ;break;}
            case 2:{ return $ServRep->find(29);break;}
            case 3:{ return $ServRep->find(19);break;}
            case 4:{ return $ServRep->find(23);break;}
            case 5:{ return $ServRep->find(30);break;}
            case 6:{ return $ServRep->find(20);break;}
            case 7:{ return $ServRep->find(10);break;}
            case 8:{ return $ServRep->find(12);break;}
            case 9:{ return $ServRep->find(22);break;}
            case 10:{ return $ServRep->find(27);break;}
            case 11:{ return $ServRep->find(28);break;}
            case 12:{ return null;break;}
            case 13:{ return $ServRep->find(31);break;}
            case 14:{ return $ServRep->find(32);break;}
            case 15:{ return $oldone;break;}
            case 16:{ return $ServRep->find(14);break;}
            case 17:{ return $ServRep->find(5);break;}
            case 18:{ return $ServRep->find(16);break;}
            case 19:{ return $ServRep->find(7);break;}
            case 20:{ return $ServRep->find(6);break;}
            case 21:{ return $ServRep->find(1);break;}
            case 22:{ return $ServRep->find(2);break;}
            case 23:{ return $ServRep->find(4);break;}
            case 24:{ return $ServRep->find(3);break;}
            case 25:{ return $ServRep->find(9);break;}
        }
        return null;
    }

    /**
     * @param string $concepto
     * @param Extension $extension
     * @param ObjectManager $em
     * @return null|Tarifa
     */
    public static function Tarifa($em,$concepto,$extension){
        return $em->getRepository(Tarifa::class)->findOneBy(['concepto'=>$concepto,'extension'=>$extension]);
    }

    /**
     * @param ObjectManager $em
     * @param Lotjuridicas $lot
     * @return Tarifa|null
     */
    public static function TarifaLot($em,Lotjuridicas $lot){
        $rama = $lot->getIdrama(); //aut, mar, ta, fc, multimodal,th
        $ext = $lot->getIdextension();//nacional,provincial
        $tposerv= $lot->getIdservicio(); //pasaje,carga o Aux con

        if($tposerv->getId()==3)
            return self::Tarifa($em,'Lots para Aux o Conex',$ext);
        if($rama->getId() ==1 || $rama->getId() ==2 || $rama->getId() ==4)
            return self::Tarifa($em,'Lots Automotor, Maritimo o Ferroviario',$ext);
        if($rama->getId()==3)
            return self::Tarifa($em,'Lots para Traccion Animal',$ext);
        if($rama->getId()==6)
            return self::Tarifa($em,'Lots para Traccion Humana',$ext);
    }
    public static function date_compare(Compestab $a, Compestab $b)
    {
        $t1 = strtotime($a->getFemitido());
        $t2 = strtotime($b->getFemitido());
        return $t1 - $t2;
    }
    /**
     * @Route("/updateestadocompmedios")
     */
    public function UpdateEstadoCompMedios(){
        $em = $this->getDoctrine()->getManager();
        $comps = $em->getRepository(Comprobante::class)->findAll();
        $compestab = $em->getRepository(Compestab::class)->findAll();
        $vencidos=0;
        foreach ($comps as $comp){
            $hoy = strtotime('today');
            $vence= strtotime($comp->getFvencimiento()->format('Y-m-d'));
            if($comp->getEstadoComp()->getId()== 4 && ($hoy - $vence) > 0){
                $comp->setEstadoComp($em->getRepository(NombEstComp::class)->find(5));
                $em->persist($comp);
                $vencidos++;
            }
        }
        foreach ($compestab as $comp){
            $hoy = strtotime('today');
            $vence= strtotime($comp->getFvencimiento()->format('Y-m-d'));
            if($comp->getEstadoComp()->getId()== 4 && ($hoy - $vence) > 0){
                $comp->setEstadoComp($em->getRepository(NombEstComp::class)->find(5));
                $em->persist($comp);
                $vencidos++;
            }
        }
        $em->flush();
        $this->addFlash('success',"Se ha modificado el estado a un total de $vencidos comprobantes");
         return $this->render('mainportada.html.twig');
    }

    public function exportXls(){
        $doc = new Spreadsheet();
    }

    public function getData(): array{
        /**
         * @var $user User[]
         */
        $list = [];
        $users = $this->getDoctrine()->getManager()->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $list[] = [
                $user->getEmail(),
                $user->getMunicipio()->getMunicipio(),
            ];
        }
        return $list;
    }
    /**
     * @Route("/export",  name="export")
     */
    public function export()
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('User List');

        $sheet->getCell('A1')->setValue('Mail');
        $sheet->getCell('B1')->setValue('Mcpio');

        // Increase row cursor after header write
        $sheet->fromArray($this->getData(),null, 'A2', true);


        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

//        $writer->save('helloworld.xlsx');

        // Crear archivo temporal en el sistema
        $fileName = 'report.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);

        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

//        return $this->redirectToRoute('portada');
    }

    /**
     * @Route("/exportingresos",name="export_ingresos")
     */

     public function exportIngresos(){        
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'serif');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // return $this->render('lotjuridicas/lotpdf.html.twig', [
        //     'title' => "Licencia de operacion de transporte $comp",'comp'=>$comp,'folio'=>$folio
        // ]);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('ingresos/printable.html.twig', [
            'title' => "Licencia de operacion de transporte 123",
            'comp'=>123,
            'folio'=>1354
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        $filename="797987";
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
    /**
     * @Route("/consultaringresos",name="consultar_ingresos")
     */

     public function ConsultarIngresos(Request $request){  
        $em= $this->getDoctrine()->getManager();
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('inicio', null,[
                'attr'=>['class'=>'form-control datetimepicker4'],
            ])
            ->add('fin', null,[
                'attr'=>['class'=>'form-control datetimepicker4'],
            ])
            ->add('Provincia',EntityType::class,[
                'class'=>Provincias::class
            ])
            ->add('Consultar',SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() ){            
            
            $inicio = $form["inicio"]->getData();
            $fin =  $form["fin"]->getData();
            $prov = $form["Provincia"]->getData();

            $db = $this->getDoctrine()->getConnection();
    
            $sql = "SELECT sum(`importe`) as 'importe',`concepto`,`provid`, count(*) as 'cant' FROM `ingresoshoygral` where `provid`=:prov GROUP by `concepto` ";
            $stmt = $db->prepare($sql);
            $params = array('prov'=>$prov->getId());
            $stmt->execute($params);
            $ingresos=$stmt->fetchAll();
           
            


            $this->addFlash('success', "Se han realizado los cambios satisfactoriamente");  
        
        }
         
        return $this->render('Sitio/consultarIngresos.html.twig',[
            'form'=>$form->createView()
        ]);
     }

}
