<?php

namespace App\Controller;

use App\Entity\Lotjuridicas;
use App\Entity\MediosTrans;
use App\Entity\Municipios;
use App\Entity\Provincias;
use App\Repository\LotjuridicasRepository;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/estadistica")
 */
class EstadisticaController extends AbstractController
{
    /**
     * @Route("/", name="estadistica")
     */
    public function index()
    {
        $user = $this->getUser();
        $idmun= $user->getMunicipio()->getId();
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT * FROM `lotsxestadoxmcpio` where `idmun`=:idmun";
        $stmt = $db->prepare($sql);
        $params = array('idmun'=>$idmun);
        $stmt->execute($params);
        $datosEstLot=$stmt->fetchAll();

        $medios = $this->getDoctrine()->getManager()->getRepository(MediosTrans::class)->findMediosbyMun($user->getMunicipio());
        $aptos = $this->getDoctrine()->getManager()->getRepository(MediosTrans::class)->findMediosAptosbyMun($user->getMunicipio());

        $sql = "SELECT * FROM `resumenlotsestadomcpio` where `idmun`=:idmun";
        $stmt = $db->prepare($sql);
        $params = array('idmun'=>$idmun);
        $stmt->execute($params);
        $medioscomp=$stmt->fetchAll();

//        return $this->render('estadistica/provincial.html.twig',[
//            'datosEstLot' => $this->EstadoLotxmcpio($user->getMunicipio()->getProvinciaid()->getId()),
//            'alcancevalor' => $user->getMunicipio(),
//            'municipios'=>$this->getDoctrine()->getRepository(Municipios::class)->findBy(['provinciaid'=>9]),
//        ]);



        return $this->render('estadistica/index.html.twig',[
            'datosEstLot' => $datosEstLot,
            'alcancevalor' => $user->getMunicipio(),
            'mediosT'=>$medios,
            'aptos' =>$aptos,
            'medioscomp'=>$medioscomp,
        ]);

    }

    /**
     * @Route("/snl/{id}", name="estadistica_SNL")
     */
    public function snl(string $id, LotjuridicasRepository $lotjuridicasRepository){
       switch ($id){
           case '0701':
               return $this->Carga($lotjuridicasRepository);break;
           case '0702':
               return $this->Pasaje($lotjuridicasRepository);break;
           case '0703':
               return $this->Consolidado($lotjuridicasRepository);break;
           case '0722':
               return $this->Instalaciones($lotjuridicasRepository);break;
           default :{
               return $this->render('mainportada.html.twig');
           }
       }
    }

    /**
     * @Route("/{prov}/snl/{id}", name="estadisticaprov_SNL")
     */
    public function snlprov(Provincias $prov,string $id, LotjuridicasRepository $lotjuridicasRepository){
        switch ($id){
            case '0701':
                return $this->CargaProv($prov,$lotjuridicasRepository);break;
            case '0702':
                return $this->PasajeProv($prov,$lotjuridicasRepository);break;
            case '0703':
                return $this->ConsolidadoProv($prov,$lotjuridicasRepository);break;
            case '0722':
                return $this->InstalacionesProv($prov,$lotjuridicasRepository);break;
            default :{
                return $this->render('mainportada.html.twig');
            }
        }
    }

    /**
     * @Route("/ingresos", name="ingresos" )
     */
    public function Ingresos(){

    }

    /**
     * @param LotjuridicasRepository $lotjuridicasRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function Carga(LotjuridicasRepository $lotjuridicasRepository){
        $db = $this->getDoctrine()->getConnection();
        $sql = "select \n"
            . " r.Ramas, r.id as idrama,\n"
            . "  sum( if(l.IDTipo=1,1,0) ) as 'Publica',\n"
            . "  sum( if(l.IDTipo=2,1,0) ) as 'Limitada',\n"
            . "  sum( if(l.IDTipo=3,1,0) ) as 'Propia',\n"
            . "  count(l.NuLicencia) as 'Total' \n"
            . "  FROM `lotjuridicas` l inner join tipo_servicio ts on l.idservicio= ts.id inner join ramas r on l.idrama = r.id INNER JOIN tipo_lot tl on tl.id= l.`IDTipo`\n"
            . "where ts.id =2 and idestado=5 \n"
            . "group by r.Ramas";
        $stmt = $db->prepare($sql);
        $params = array();
        $stmt->execute($params);
        $lotvigcarga=$stmt->fetchAll();
        $lots= $lotjuridicasRepository->findBy(['idservicio'=>2,'idestado'=>5]);
        $sql = "select `tm`.`Medios` AS `Medios`,sum(if(`l`.`IDTipo` = 1,1,0)) AS `Publica`,sum(if(`l`.`IDTipo` = 2,1,0)) AS `Limitada`,sum(if(`l`.`IDTipo` = 3,1,0)) AS `Propia`,count(`m`.`id`) AS `Total` from ((((`lojurid_db`.`medios_trans` `m` join `lojurid_db`.`basificacion` `b` on(`m`.`basificacionObj` = `b`.`IdLBasiAM`)) join `lojurid_db`.`lotjuridicas` `l` on(`b`.`idlicencia` = `l`.`NuLicencia`)) join `lojurid_db`.`comprobante` `c` on(`m`.`id` = `c`.`medio_id`)) join `lojurid_db`.`tipomedio` `tm` on(`m`.`tipomedio` = `tm`.`IdMedio`)) where `l`.`IDServicio` = 2 and `l`.`IDEstado` = 5 and `m`.`rama` = :rama and `c`.`estado_comp_id` = 4 group by `tm`.`Medios`";
        $stmt1 = $db->prepare($sql);
        $params = array('rama'=>1);
        $stmt1->execute($params);
        $mediosAm=$stmt1->fetchAll();
        $params = array('rama'=>2);
        $stmt1->execute($params);
        $mediosMar=$stmt1->fetchAll();
        $params = array('rama'=>4);
        $stmt1->execute($params);
        $mediosFC=$stmt1->fetchAll();
        $params = array('rama'=>3);
        $stmt1->execute($params);
        $mediosTA=$stmt1->fetchAll();
        $params = array('rama'=>6);
        $stmt1->execute($params);
        $mediosTH=$stmt1->fetchAll();

        return $this->render('estadistica/SNL0701.html.twig',[
            'lotvigcargaSum'=>$lotvigcarga,
            'lots'=>$lots,
            'mediosAm'=>$mediosAm,
            'mediosMar'=>$mediosMar,
            'mediosFC'=>$mediosFC,
            'mediosTA'=>$mediosTA,
            'mediosTH'=>$mediosTH,
        ]);
    }
    private function CargaProv(Provincias $prov,LotjuridicasRepository $lotjuridicasRepository){
        $db = $this->getDoctrine()->getConnection();
        $sql = "select \n"
            . " r.Ramas, r.id as idrama,\n"
            . "  sum( if(l.IDTipo=1,1,0) ) as 'Publica',\n"
            . "  sum( if(l.IDTipo=2,1,0) ) as 'Limitada',\n"
            . "  sum( if(l.IDTipo=3,1,0) ) as 'Propia',\n"
            . "  count(l.NuLicencia) as 'Total' \n"
            . "  FROM `lotjuridicas` l inner join tipo_servicio ts on l.idservicio= ts.id inner join ramas r on l.idrama = r.id INNER JOIN tipo_lot tl on tl.id= l.`IDTipo` INNER join personasjuridicas pj on pj.IdEntidad = l.identidad inner join municipios m on m.ID = pj.idmunicipio inner join provincias prov on m.provinciaid = prov.ID\n"
            . "where ts.id =2 and idestado=5 and prov.ID=:prov \n"
            . "group by r.Ramas";
        $stmt = $db->prepare($sql);
        $params = array('prov'=>$prov->getId());
        $stmt->execute($params);
        $lotvigcarga=$stmt->fetchAll();
//        $lots= $lotjuridicasRepository->findBy(['idservicio'=>2,'idestado'=>5]);
        $sql = "select `tm`.`Medios` AS `Medios`,sum(if(`l`.`IDTipo` = 1,1,0)) AS `Publica`,sum(if(`l`.`IDTipo` = 2,1,0)) AS `Limitada`,sum(if(`l`.`IDTipo` = 3,1,0)) AS `Propia`,count(`m`.`id`) AS `Total` from ((((`lojurid_db`.`medios_trans` `m` join `lojurid_db`.`basificacion` `b` on(`m`.`basificacionObj` = `b`.`IdLBasiAM`)) join `lojurid_db`.`lotjuridicas` `l` on(`b`.`idlicencia` = `l`.`NuLicencia`)) join `lojurid_db`.`comprobante` `c` on(`m`.`id` = `c`.`medio_id`)) join `lojurid_db`.`tipomedio` `tm` on(`m`.`tipomedio` = `tm`.`IdMedio`)) join municipios mun on mun.ID= b.idmun inner join provincias prov on prov.ID = mun.provinciaid  where `l`.`IDServicio` = 2 and `l`.`IDEstado` = 5 and `m`.`rama` = :rama and `c`.`estado_comp_id` = 4 and prov.ID=:prov group by `tm`.`Medios`";
        $stmt1 = $db->prepare($sql);
        $params = array('rama'=>1,'prov'=>$prov->getId());
        $stmt1->execute($params);
        $mediosAm=$stmt1->fetchAll();
        $params = array('rama'=>2,'prov'=>$prov->getId());
        $stmt1->execute($params);
        $mediosMar=$stmt1->fetchAll();
        $params = array('rama'=>4,'prov'=>$prov->getId());
        $stmt1->execute($params);
        $mediosFC=$stmt1->fetchAll();
        $params = array('rama'=>3,'prov'=>$prov->getId());
        $stmt1->execute($params);
        $mediosTA=$stmt1->fetchAll();
        $params = array('rama'=>6,'prov'=>$prov->getId());
        $stmt1->execute($params);
        $mediosTH=$stmt1->fetchAll();

        return $this->render('estadistica/SNL0701prov.html.twig',[
            'lotvigcargaSum'=>$lotvigcarga,
            'prov'=>$prov,
            'mediosAm'=>$mediosAm,
            'mediosMar'=>$mediosMar,
            'mediosFC'=>$mediosFC,
            'mediosTA'=>$mediosTA,
            'mediosTH'=>$mediosTH,
        ]);
    }
    private function Pasaje(LotjuridicasRepository $lotjuridicasRepository){
        $db = $this->getDoctrine()->getConnection();
        $sql = "select \n"
            . " r.Ramas,r.id as idrama,\n"
            . "  sum( if(l.IDTipo=1,1,0) ) as 'Publica',\n"
            . "  sum( if(l.IDTipo=2,1,0) ) as 'Limitada',\n"
            . "  sum( if(l.IDTipo=3,1,0) ) as 'Propia',\n"
            . "  count(l.NuLicencia) as 'Total' \n"
            . "  FROM `lotjuridicas` l inner join tipo_servicio ts on l.idservicio= ts.id inner join ramas r on l.idrama = r.id INNER JOIN tipo_lot tl on tl.id= l.`IDTipo`\n"
            . "where ts.id =1 and idestado=5 \n"
            . "group by r.Ramas";
        $stmt = $db->prepare($sql);
        $params = array();
        $stmt->execute($params);
        $lotvigcarga=$stmt->fetchAll();
        $lots= $lotjuridicasRepository->findBy(['idservicio'=>1,'idestado'=>5]);
        $sql = "select `tm`.`Medios` AS `Medios`,sum(if(`l`.`IDTipo` = 1,1,0)) AS `Publica`,sum(if(`l`.`IDTipo` = 2,1,0)) AS `Limitada`,sum(if(`l`.`IDTipo` = 3,1,0)) AS `Propia`,count(`m`.`id`) AS `Total` from ((((`lojurid_db`.`medios_trans` `m` join `lojurid_db`.`basificacion` `b` on(`m`.`basificacionObj` = `b`.`IdLBasiAM`)) join `lojurid_db`.`lotjuridicas` `l` on(`b`.`idlicencia` = `l`.`NuLicencia`)) join `lojurid_db`.`comprobante` `c` on(`m`.`id` = `c`.`medio_id`)) join `lojurid_db`.`tipomedio` `tm` on(`m`.`tipomedio` = `tm`.`IdMedio`)) where `l`.`IDServicio` = 1 and `l`.`IDEstado` = 5 and `m`.`rama` = :rama and `c`.`estado_comp_id` = 4 group by `tm`.`Medios`";
        $stmt1 = $db->prepare($sql);
        $params = array('rama'=>1);
        $stmt1->execute($params);
        $mediosAm=$stmt1->fetchAll();
        $params = array('rama'=>2);
        $stmt1->execute($params);
        $mediosMar=$stmt1->fetchAll();
        $params = array('rama'=>4);
        $stmt1->execute($params);
        $mediosFC=$stmt1->fetchAll();
        $params = array('rama'=>3);
        $stmt1->execute($params);
        $mediosTA=$stmt1->fetchAll();
        $params = array('rama'=>6);
        $stmt1->execute($params);
        $mediosTH=$stmt1->fetchAll();
        return $this->render('estadistica/SNL0702.html.twig',[
            'lotvigcargaSum'=>$lotvigcarga,
            'lots'=>$lots,
            'mediosAm'=>$mediosAm,
            'mediosMar'=>$mediosMar,
            'mediosFC'=>$mediosFC,
            'mediosTA'=>$mediosTA,
            'mediosTH'=>$mediosTH,
        ]);
    }
    private function PasajeProv(Provincias $prov,LotjuridicasRepository $lotjuridicasRepository){
        $db = $this->getDoctrine()->getConnection();
        $sql = "select \n"
            . " r.Ramas, r.id as idrama,\n"
            . "  sum( if(l.IDTipo=1,1,0) ) as 'Publica',\n"
            . "  sum( if(l.IDTipo=2,1,0) ) as 'Limitada',\n"
            . "  sum( if(l.IDTipo=3,1,0) ) as 'Propia',\n"
            . "  count(l.NuLicencia) as 'Total' \n"
            . "  FROM `lotjuridicas` l inner join tipo_servicio ts on l.idservicio= ts.id inner join ramas r on l.idrama = r.id INNER JOIN tipo_lot tl on tl.id= l.`IDTipo` INNER join personasjuridicas pj on pj.IdEntidad = l.identidad inner join municipios m on m.ID = pj.idmunicipio inner join provincias prov on m.provinciaid = prov.ID\n"
            . "where ts.id =1 and idestado=5 and prov.ID=:prov\n"
            . "group by r.Ramas";
        $stmt = $db->prepare($sql);
        $params = array('prov'=>$prov->getId());
        $stmt->execute($params);
        $lotvigcarga=$stmt->fetchAll();
//        $lots= $lotjuridicasRepository->findBy(['idservicio'=>2,'idestado'=>5]);
        $sql = "select `tm`.`Medios` AS `Medios`,sum(if(`l`.`IDTipo` = 1,1,0)) AS `Publica`,sum(if(`l`.`IDTipo` = 2,1,0)) AS `Limitada`,sum(if(`l`.`IDTipo` = 3,1,0)) AS `Propia`,count(`m`.`id`) AS `Total` from ((((`lojurid_db`.`medios_trans` `m` join `lojurid_db`.`basificacion` `b` on(`m`.`basificacionObj` = `b`.`IdLBasiAM`)) join `lojurid_db`.`lotjuridicas` `l` on(`b`.`idlicencia` = `l`.`NuLicencia`)) join `lojurid_db`.`comprobante` `c` on(`m`.`id` = `c`.`medio_id`)) join `lojurid_db`.`tipomedio` `tm` on(`m`.`tipomedio` = `tm`.`IdMedio`)) join municipios mun on mun.ID= b.idmun inner join provincias prov on prov.ID = mun.provinciaid  where `l`.`IDServicio` = 1 and `l`.`IDEstado` = 5 and `m`.`rama` = :rama and `c`.`estado_comp_id` = 4 and prov.ID=:prov group by `tm`.`Medios`";
        $stmt1 = $db->prepare($sql);
        $params = array('rama'=>1,'prov'=>$prov->getId());
        $stmt1->execute($params);
        $mediosAm=$stmt1->fetchAll();
        $params = array('rama'=>2,'prov'=>$prov->getId());
        $stmt1->execute($params);
        $mediosMar=$stmt1->fetchAll();
        $params = array('rama'=>4,'prov'=>$prov->getId());
        $stmt1->execute($params);
        $mediosFC=$stmt1->fetchAll();
        $params = array('rama'=>3,'prov'=>$prov->getId());
        $stmt1->execute($params);
        $mediosTA=$stmt1->fetchAll();
        $params = array('rama'=>6,'prov'=>$prov->getId());
        $stmt1->execute($params);
        $mediosTH=$stmt1->fetchAll();

        return $this->render('estadistica/SNL0702prov.html.twig',[
            'lotvigcargaSum'=>$lotvigcarga,
            'prov'=>$prov,
            'mediosAm'=>$mediosAm,
            'mediosMar'=>$mediosMar,
            'mediosFC'=>$mediosFC,
            'mediosTA'=>$mediosTA,
            'mediosTH'=>$mediosTH,
        ]);
    }
    private function Consolidado(LotjuridicasRepository $lotjuridicasRepository){
        $db = $this->getDoctrine()->getConnection();
        $sql = "select \n"
            . " r.Ramas,\n"
            . "  sum( if(l.IDTipo=1,1,0) ) as 'Publica',\n"
            . "  sum( if(l.IDTipo=2,1,0) ) as 'Limitada',\n"
            . "  sum( if(l.IDTipo=3,1,0) ) as 'Propia',\n"
            . "  count(l.NuLicencia) as 'Total' \n"
            . "  FROM `lotjuridicas` l inner join tipo_servicio ts on l.idservicio= ts.id inner join ramas r on l.idrama = r.id INNER JOIN tipo_lot tl on tl.id= l.`IDTipo`\n"
            . "where idestado=5 \n"
            . "group by r.Ramas";
        $stmt = $db->prepare($sql);
        $params = array();
        $stmt->execute($params);
        $lotvigcarga=$stmt->fetchAll();


        $sql = "select \n"
            . " r.Ramas,\n"
            . "  sum( if(l.IDTipo=1,1,0) ) as 'Publica',\n"
            . "  sum( if(l.IDTipo=2,1,0) ) as 'Limitada',\n"
            . "  sum( if(l.IDTipo=3,1,0) ) as 'Propia',\n"
            . "  count(l.NuLicencia) as 'Total' \n"
            . "  FROM `lotjuridicas` l inner join tipo_servicio ts on l.idservicio= ts.id inner join ramas r on l.idrama = r.id INNER JOIN tipo_lot tl on tl.id= l.`IDTipo`\n"
            . "where idestado=5 and l.idservicio=3 \n"
            . "group by r.Ramas";
        $stmt1 = $db->prepare($sql);
        $params = array();
        $stmt1->execute($params);
        $lotsAux=$stmt1->fetchAll();

        $lots= $lotjuridicasRepository->findBy(['idestado'=>5]);

        $sql = "select `tm`.`Medios` AS `Medios`,sum(if(`l`.`IDTipo` = 1,1,0)) AS `Publica`,sum(if(`l`.`IDTipo` = 2,1,0)) AS `Limitada`,sum(if(`l`.`IDTipo` = 3,1,0)) AS `Propia`,count(`m`.`id`) AS `Total` from ((((`lojurid_db`.`medios_trans` `m` join `lojurid_db`.`basificacion` `b` on(`m`.`basificacionObj` = `b`.`IdLBasiAM`)) join `lojurid_db`.`lotjuridicas` `l` on(`b`.`idlicencia` = `l`.`NuLicencia`)) join `lojurid_db`.`comprobante` `c` on(`m`.`id` = `c`.`medio_id`)) join `lojurid_db`.`tipomedio` `tm` on(`m`.`tipomedio` = `tm`.`IdMedio`)) where `l`.`IDEstado` = 5 and `m`.`rama` = :rama and `c`.`estado_comp_id` = 4 group by `tm`.`Medios`";
        $stmt1 = $db->prepare($sql);
        $params = array('rama'=>1);
        $stmt1->execute($params);
        $mediosAm=$stmt1->fetchAll();
        $params = array('rama'=>2);
        $stmt1->execute($params);
        $mediosMar=$stmt1->fetchAll();
        $params = array('rama'=>4);
        $stmt1->execute($params);
        $mediosFC=$stmt1->fetchAll();
        $params = array('rama'=>3);
        $stmt1->execute($params);
        $mediosTA=$stmt1->fetchAll();
        $params = array('rama'=>6);
        $stmt1->execute($params);
        $mediosTH=$stmt1->fetchAll();
        return $this->render('estadistica/SNL0703.html.twig',[
            'lotvigcargaSum'=>$lotvigcarga,
            'lots'=>$lots,
            'mediosAm'=>$mediosAm,
            'mediosMar'=>$mediosMar,
            'mediosFC'=>$mediosFC,
            'mediosTA'=>$mediosTA,
            'mediosTH'=>$mediosTH,
            'lotsAux'=>$lotsAux,
        ]);
    }
    private function ConsolidadoProv(Provincias $prov,LotjuridicasRepository $lotjuridicasRepository){
        $db = $this->getDoctrine()->getConnection();
        $sql = "select \n"
            . " r.Ramas,\n"
            . "  sum( if(l.IDTipo=1,1,0) ) as 'Publica',\n"
            . "  sum( if(l.IDTipo=2,1,0) ) as 'Limitada',\n"
            . "  sum( if(l.IDTipo=3,1,0) ) as 'Propia',\n"
            . "  count(l.NuLicencia) as 'Total' \n"
            . "  FROM `lotjuridicas` l inner join tipo_servicio ts on l.idservicio= ts.id inner join ramas r on l.idrama = r.id INNER JOIN tipo_lot tl on tl.id= l.`IDTipo` inner join personasjuridicas pj on pj.IdEntidad = l.identidad inner join municipios m on m.ID = pj.idmunicipio inner join provincias prov on m.provinciaid = prov.ID\n"
            . "where idestado=5 and prov.ID=:prov \n"
            . "group by r.Ramas";
        $stmt = $db->prepare($sql);
        $params = array('prov'=>$prov->getId());
//        $params = array();
        $stmt->execute($params);
        $lotvigcarga=$stmt->fetchAll();


        $sql = "select \n"
            . " r.Ramas,\n"
            . "  sum( if(l.IDTipo=1,1,0) ) as 'Publica',\n"
            . "  sum( if(l.IDTipo=2,1,0) ) as 'Limitada',\n"
            . "  sum( if(l.IDTipo=3,1,0) ) as 'Propia',\n"
            . "  count(l.NuLicencia) as 'Total' \n"
            . "  FROM `lotjuridicas` l inner join tipo_servicio ts on l.idservicio= ts.id inner join ramas r on l.idrama = r.id INNER JOIN tipo_lot tl on tl.id= l.`IDTipo` inner join personasjuridicas pj on pj.IdEntidad = l.identidad inner join municipios m on m.ID = pj.idmunicipio inner join provincias prov on m.provinciaid = prov.ID \n"
            . "where idestado=5 and l.idservicio=3 and prov.ID=:prov\n"
            . "group by r.Ramas";
        $stmt1 = $db->prepare($sql);
        $params = array('prov'=>$prov->getId());
        $stmt1->execute($params);
        $lotsAux=$stmt1->fetchAll();

        $lots= $lotjuridicasRepository->findBy(['idestado'=>5]);

        $sql = "select `tm`.`Medios` AS `Medios`,sum(if(`l`.`IDTipo` = 1,1,0)) AS `Publica`,sum(if(`l`.`IDTipo` = 2,1,0)) AS `Limitada`,sum(if(`l`.`IDTipo` = 3,1,0)) AS `Propia`,count(`m`.`id`) AS `Total` from ((((`lojurid_db`.`medios_trans` `m` join `lojurid_db`.`basificacion` `b` on(`m`.`basificacionObj` = `b`.`IdLBasiAM`)) join `lojurid_db`.`lotjuridicas` `l` on(`b`.`idlicencia` = `l`.`NuLicencia`)) join `lojurid_db`.`comprobante` `c` on(`m`.`id` = `c`.`medio_id`)) join `lojurid_db`.`tipomedio` `tm` on(`m`.`tipomedio` = `tm`.`IdMedio`)) join municipios mun on mun.ID= b.idmun inner join provincias prov on prov.ID = mun.provinciaid where `l`.`IDEstado` = 5 and `m`.`rama` = :rama and `c`.`estado_comp_id` = 4 and prov.ID=:prov group by `tm`.`Medios`";
        $stmt1 = $db->prepare($sql);
        $params = array('rama'=>1,'prov'=>$prov->getId());
        $stmt1->execute($params);
        $mediosAm=$stmt1->fetchAll();
        $params = array('rama'=>2,'prov'=>$prov->getId());
        $stmt1->execute($params);
        $mediosMar=$stmt1->fetchAll();
        $params = array('rama'=>4,'prov'=>$prov->getId());
        $stmt1->execute($params);
        $mediosFC=$stmt1->fetchAll();
        $params = array('rama'=>3,'prov'=>$prov->getId());
        $stmt1->execute($params);
        $mediosTA=$stmt1->fetchAll();
        $params = array('rama'=>6,'prov'=>$prov->getId());
        $stmt1->execute($params);
        $mediosTH=$stmt1->fetchAll();
        return $this->render('estadistica/SNL0703prov.html.twig',[
            'lotvigcargaSum'=>$lotvigcarga,
            'prov'=>$prov,
            'mediosAm'=>$mediosAm,
            'mediosMar'=>$mediosMar,
            'mediosFC'=>$mediosFC,
            'mediosTA'=>$mediosTA,
            'mediosTH'=>$mediosTH,
            'lotsAux'=>$lotsAux,
        ]);
    }
    private function Instalaciones(LotjuridicasRepository $lotjuridicasRepository){
        $db = $this->getDoctrine()->getConnection();
        $sql = "select tsa.ServAuxCon, sum( if(l.IDTipo=1,1,0) ) as 'Publica',\n"

            . "		sum( if(l.IDTipo=2,1,0) ) as 'Limitada',\n"

            . "        sum( if(l.IDTipo=3,1,0) ) as 'Propia',\n"

            . "        count(l.NuLicencia) as 'Total' \n"

            . "FROM `lotjuridicas` l inner join tipo_servicio ts on l.idservicio= ts.id INNER JOIN tipo_lot tl on tl.id= l.`IDTipo`  inner join instalaciones i on i.lot_id = l.NuLicencia inner join compestab c on i.id = c.instalacion inner join tipo_serv_aux_con tsa on i.`serv_aux_con_id` = tsa.id\n"

            . "           where idestado=5 and c.estado_comp_id = 4\n"

            . "            group by tsa.ServAuxCon";
        $stmt = $db->prepare($sql);
        $params = array();
        $stmt->execute($params);
        $instalaciones=$stmt->fetchAll();


        return $this->render('estadistica/SNL0722.html.twig',[
            'instalaciones'=>$instalaciones,
        ]);
    }
    private function InstalacionesProv(Provincias $prov,LotjuridicasRepository $lotjuridicasRepository){
        $db = $this->getDoctrine()->getConnection();
        $sql = "select * from `totalinstalacionesporservicioor` tp where tp.provid =:prov ";
        $stmt = $db->prepare($sql);
        $params = array('prov'=>$prov->getId());
        $stmt->execute($params);
        $instalaciones=$stmt->fetchAll();

        $sql = "select sum(`total`) as 'total',sum(`Publicas`) as 'tpublicas', sum(`Propias`) as 'tpropias',sum(`Limitadas`) as 'tlimitadas' from `totalinstalacionesporservicioor` tp where tp.provid =:prov ";
        $stmt1 = $db->prepare($sql);
        $params = array('prov'=>$prov->getId());
        $stmt1->execute($params);
        $totales=$stmt1->fetchAll();

        return $this->render('estadistica/SNL0722prov.html.twig',[
            'prov'=>$prov,
            'instalaciones'=>$instalaciones,
            'totales'=>$totales,
        ]);
    }
    private function EstadoLotxmcpio(int $prov){
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT * FROM `lotsxestadoxmcpio` where `provinciaid`=:prov";
        $stmt = $db->prepare($sql);
        $params = array('prov'=>$prov);
        $stmt->execute($params);
        $datosEstLot=$stmt->fetchAll();
        return $datosEstLot;
    }

    /**
     * @Route("/export/{alcance}/{alcancevalor}", name="estadistica_xls")
     */
    public function ExportMod1Xls($alcance,$alcancevalor = null){
        switch ($alcance){
            case 1:
                return $this->datosModelo1Nac();
            case 2:
                return $this->datosModelo1Prov($this->getDoctrine()->getManager()->getRepository(Provincias::class)->find($alcancevalor));
            default:
                return $this->datosModelo1Mcpal($this->getDoctrine()->getManager()->getRepository(Municipios::class)->find($alcancevalor));
        }


//       $data= $this->EstadoLotxmcpio( $this->getUser()->getMunicipio()->getProvinciaid()->getId());
//       return $this->export($data);
//        $data = $this->LotsNac();
//        return $this->exportLotsxestadoxProv($data);
//        return $this->datosModelo1Nac();
//        return $this->datosModelo1Prov($this->getDoctrine()->getManager()->getRepository(Provincias::class)->find(9));
//        return $this->datosModelo1Mcpal($this->getDoctrine()->getManager()->getRepository(Municipios::class)->find(109));
    }

    private function DatosModelo1(int $alcance,int $alcancevalor){
        switch ($alcance){
            case 1: $where = " where `codmun`=:valor";
                    break;
            case 2: $where = " where `codprov`=:valor";
                break;
            default: $where = " where 1"; break;
        }
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT * FROM `datosmodelo1xmcpio`";
        $stmt = $db->prepare($sql.$where);
        $params = array('valor'=>$alcancevalor);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function DatosMedios(int $alcance,int $alcancevalor=null){
        switch ($alcance){
            case 1: $where = " where `munid`=:valor";
                break;
            case 2: $where = " where `provid`=:valor";
                break;
            default: $where = " where 1"; break;
        }
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT * FROM `mediosxmcpio`";
        $stmt = $db->prepare($sql.$where);
        $params = array('valor'=>$alcancevalor);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * @Route("/datosmodelo1nac", name="estadistica_modelo1nac")
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function datosModelo1Nac(){
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT  * FROM `lotsxestadoxprov`";
        $stmt = $db->prepare($sql);
        $params = array();
        $stmt->execute($params);
        $resumen= $stmt->fetchAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Lots por estado por provincias');
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
        $spreadsheet->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleArray);
        //Sacamos el encabezado de la pagina resumen
        $sheet->getCell('A1')->setValue('Provincia');
        $sheet->getCell('B1')->setValue('Total');
        $sheet->getCell('C1')->setValue('Pte Aprob');
        $sheet->getCell('D1')->setValue('Denegada');
        $sheet->getCell('E1')->setValue('Pte Entrega');
        $sheet->getCell('F1')->setValue('No Entregada');
        $sheet->getCell('G1')->setValue('Suspendida');
        $sheet->getCell('H1')->setValue('Pte Renovacion');
        $sheet->getCell('I1')->setValue('Pte Recoger ST');
        $sheet->getCell('J1')->setValue('Destruido');
        $sheet->getCell('K1')->setValue('Pte Impresion');
        $sheet->getCell('L1')->setValue('Vigentes');
        $sheet->getCell('M1')->setValue('Canceladas');
        $estad =[];
        foreach ($resumen as $est) {
            $estad[] = [
                $est['provincia'],
                $est['total'],
                $est['Pte Aprob'],
                $est['Denegada'],
                $est['Pte Entrega'],
                $est['No Entregada'],
                $est['Suspendida'],
                $est['Pte Renovacion'],
                $est['Pte Recoger ST'],
                $est['Destruido'],
                $est['Pte Impresion'],
                $est['vigentes'],
                $est['canceladas'],
            ];
            $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $est['provincia']);
            //para cada prov creamos el encabezado para las paginas de las prov
            $myWorkSheet->getCell('A1')->setValue('NoLOT');
            $myWorkSheet->getCell('B1')->setValue('Municipio');
            $myWorkSheet->getCell('C1')->setValue('Nombre Entidad');
            $myWorkSheet->getCell('D1')->setValue('Organ.');
            $myWorkSheet->getCell('E1')->setValue('Dirección');
            $myWorkSheet->getCell('F1')->setValue('AprobIni');
            $myWorkSheet->getCell('G1')->setValue('Renov.');
            $myWorkSheet->getCell('H1')->setValue('TP');
            $myWorkSheet->getCell('I1')->setValue('Servicio');
            $myWorkSheet->getCell('J1')->setValue('Limitacion');
            $myWorkSheet->getCell('K1')->setValue('Estado');
            $myWorkSheet->getCell('L1')->setValue('Extension');
            $myWorkSheet->getCell('M1')->setValue('Medios Aut');
            $myWorkSheet->getCell('N1')->setValue('Medios Mar');
            $myWorkSheet->getCell('O1')->setValue('Medios TAnim');
            $myWorkSheet->getCell('P1')->setValue('Medios Ferr');
            $myWorkSheet->getCell('Q1')->setValue('Medios THum');
            $myWorkSheet->getCell('R1')->setValue('Medios Total');
            //una vez tengamos el encabezado proseguimos con los datos
            $list = [];
            //obtenemos los datos del modelo para cada provincia en la interacion
            $lots = $this->DatosModelo1(2,$est['provinciaid']);
            //organizamos los datos para imprimirlos al fichero xls
            foreach ($lots as $lot) {
                $list[] = [
                    $lot['noLot'],
                    $lot['municipio'],
                    $lot['NomEntidad'],
                    $lot['org'],
                    $lot['direccion'],
                    $lot['AprobIni'],
                    $lot['renov'],
                    "PP",
                    $lot['servicio'],
                    $lot['limitacion'],
                    $lot['estado'],
                    $lot['extension'],
                    $lot['autom'],
                    $lot['maritim'],
                    $lot['TA'],
                    $lot['Ferrov'],
                    $lot['TH'],
                    $lot['totalmedios'],
                ];
            }

            $spreadsheet->addSheet($myWorkSheet);
            $myWorkSheet->getStyle('A1:R1')->applyFromArray($styleArray);
            $myWorkSheet->fromArray($list,null, 'A2', true);
        }
        // Llenamos los datos de la pagina principal
        $sheet->fromArray($estad,null, 'A2', true);

        // Auto-size columns for all worksheets
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            foreach ($worksheet->getColumnIterator() as $column) {
                $worksheet
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);
            }
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Crear archivo temporal en el sistema
        $fileName = 'Modelo1nac.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);
        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/datosmodelo1prov/{prov}", name="estadistica_modelo1prov")
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function datosModelo1Prov(Provincias $prov){
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT  * FROM `lotsxestadoxmcpio` where `provinciaid`=:prov";
        $stmt = $db->prepare($sql);
        $params = array('prov'=>$prov->getId());
        $stmt->execute($params);
        $resumen= $stmt->fetchAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen');
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
        $spreadsheet->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleArray);
        //Sacamos el encabezado de la pagina resumen
        $sheet->getCell('A1')->setValue('Municipio');
        $sheet->getCell('B1')->setValue('Total');
        $sheet->getCell('C1')->setValue('Pte Aprob');
        $sheet->getCell('D1')->setValue('Denegada');
        $sheet->getCell('E1')->setValue('Pte Entrega');
        $sheet->getCell('F1')->setValue('No Entregada');
        $sheet->getCell('G1')->setValue('Suspendida');
        $sheet->getCell('H1')->setValue('Pte Renovacion');
        $sheet->getCell('I1')->setValue('Pte Recoger ST');
        $sheet->getCell('J1')->setValue('Destruido');
        $sheet->getCell('K1')->setValue('Pte Impresion');
        $sheet->getCell('L1')->setValue('Vigentes');
        $sheet->getCell('M1')->setValue('Canceladas');
        $estad =[];
        foreach ($resumen as $est) {
            $estad[] = [
                $est['Municipios'],
                $est['total'],
                $est['Pte Aprob'],
                $est['Denegada'],
                $est['Pte Entrega'],
                $est['No Entregada'],
                $est['Suspendida'],
                $est['Pte Renovacion'],
                $est['Pte Recoger ST'],
                $est['Destruido'],
                $est['Pte Impresion'],
                $est['vigentes'],
                $est['canceladas'],
            ];
            $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $est['Municipios']);
            //para cada prov creamos el encabezado para las paginas de las prov
            $myWorkSheet->getCell('A1')->setValue('NoLOT');
            $myWorkSheet->getCell('B1')->setValue('Municipio');
            $myWorkSheet->getCell('C1')->setValue('Nombre Entidad');
            $myWorkSheet->getCell('D1')->setValue('Organ.');
            $myWorkSheet->getCell('E1')->setValue('Dirección');
            $myWorkSheet->getCell('F1')->setValue('AprobIni');
            $myWorkSheet->getCell('G1')->setValue('Renov.');
            $myWorkSheet->getCell('H1')->setValue('TP');
            $myWorkSheet->getCell('I1')->setValue('Servicio');
            $myWorkSheet->getCell('J1')->setValue('Limitacion');
            $myWorkSheet->getCell('K1')->setValue('Estado');
            $myWorkSheet->getCell('L1')->setValue('Extension');
            $myWorkSheet->getCell('M1')->setValue('Medios Aut');
            $myWorkSheet->getCell('N1')->setValue('Medios Mar');
            $myWorkSheet->getCell('O1')->setValue('Medios TAnim');
            $myWorkSheet->getCell('P1')->setValue('Medios Ferr');
            $myWorkSheet->getCell('Q1')->setValue('Medios THum');
            $myWorkSheet->getCell('R1')->setValue('Medios Total');
            //una vez tengamos el encabezado proseguimos con los datos
            $list = [];
            //obtenemos los datos del modelo para cada provincia en la interacion
            $lots = $this->DatosModelo1(1,$est['idmun']);
            //organizamos los datos para imprimirlos al fichero xls
            foreach ($lots as $lot) {
                $list[] = [
                    $lot['noLot'],
                    $lot['municipio'],
                    $lot['NomEntidad'],
                    $lot['org'],
                    $lot['direccion'],
                    $lot['AprobIni'],
                    $lot['renov'],
                    "PP",
                    $lot['servicio'],
                    $lot['limitacion'],
                    $lot['estado'],
                    $lot['extension'],
                    $lot['autom'],
                    $lot['maritim'],
                    $lot['TA'],
                    $lot['Ferrov'],
                    $lot['TH'],
                    $lot['totalmedios'],
                ];
            }

            $spreadsheet->addSheet($myWorkSheet);
            $myWorkSheet->getStyle('A1:R1')->applyFromArray($styleArray);
            $myWorkSheet->fromArray($list,null, 'A2', true);
        }
        // Llenamos los datos de la pagina principal
        $sheet->fromArray($estad,null, 'A2', true);

        // Auto-size columns for all worksheets
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            foreach ($worksheet->getColumnIterator() as $column) {
                $worksheet
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);
            }
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Crear archivo temporal en el sistema
        $fileName = 'Modelo1 '.$prov->getProvincias().'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);
        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/datosmodelo1mcpal/{mcpio}", name="estadistica_modelo1mcpal")
     * @param Municipios $mcpio
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function datosModelo1Mcpal(Municipios $mcpio){
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT  * FROM `lotsxestadoxmcpio` where `idmun`=:mcpio";
        $stmt = $db->prepare($sql);
        $params = array('mcpio'=>$mcpio->getId());
        $stmt->execute($params);
        $resumen= $stmt->fetchAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen');
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
        $spreadsheet->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleArray);
        //Sacamos el encabezado de la pagina resumen
        $sheet->getCell('A1')->setValue('Municipio');
        $sheet->getCell('B1')->setValue('Total');
        $sheet->getCell('C1')->setValue('Pte Aprob');
        $sheet->getCell('D1')->setValue('Denegada');
        $sheet->getCell('E1')->setValue('Pte Entrega');
        $sheet->getCell('F1')->setValue('No Entregada');
        $sheet->getCell('G1')->setValue('Suspendida');
        $sheet->getCell('H1')->setValue('Pte Renovacion');
        $sheet->getCell('I1')->setValue('Pte Recoger ST');
        $sheet->getCell('J1')->setValue('Destruido');
        $sheet->getCell('K1')->setValue('Pte Impresion');
        $sheet->getCell('L1')->setValue('Vigentes');
        $sheet->getCell('M1')->setValue('Canceladas');
        $estad =[];
        foreach ($resumen as $est) {
            $estad[] = [
                $est['Municipios'],
                $est['total'],
                $est['Pte Aprob'],
                $est['Denegada'],
                $est['Pte Entrega'],
                $est['No Entregada'],
                $est['Suspendida'],
                $est['Pte Renovacion'],
                $est['Pte Recoger ST'],
                $est['Destruido'],
                $est['Pte Impresion'],
                $est['vigentes'],
                $est['canceladas'],
            ];
            $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $est['Municipios']);
            //para cada prov creamos el encabezado para las paginas de las prov
            $myWorkSheet->getCell('A1')->setValue('NoLOT');
            $myWorkSheet->getCell('B1')->setValue('Municipio');
            $myWorkSheet->getCell('C1')->setValue('Nombre Entidad');
            $myWorkSheet->getCell('D1')->setValue('Organ.');
            $myWorkSheet->getCell('E1')->setValue('Dirección');
            $myWorkSheet->getCell('F1')->setValue('AprobIni');
            $myWorkSheet->getCell('G1')->setValue('Renov.');
            $myWorkSheet->getCell('H1')->setValue('TP');
            $myWorkSheet->getCell('I1')->setValue('Servicio');
            $myWorkSheet->getCell('J1')->setValue('Limitacion');
            $myWorkSheet->getCell('K1')->setValue('Estado');
            $myWorkSheet->getCell('L1')->setValue('Extension');
            $myWorkSheet->getCell('M1')->setValue('Medios Aut');
            $myWorkSheet->getCell('N1')->setValue('Medios Mar');
            $myWorkSheet->getCell('O1')->setValue('Medios TAnim');
            $myWorkSheet->getCell('P1')->setValue('Medios Ferr');
            $myWorkSheet->getCell('Q1')->setValue('Medios THum');
            $myWorkSheet->getCell('R1')->setValue('Medios Total');
            //una vez tengamos el encabezado proseguimos con los datos
            $list = [];
            //obtenemos los datos del modelo para cada provincia en la interacion
            $lots = $this->DatosModelo1(1,$est['idmun']);
            //organizamos los datos para imprimirlos al fichero xls
            foreach ($lots as $lot) {
                $list[] = [
                    $lot['noLot'],
                    $lot['municipio'],
                    $lot['NomEntidad'],
                    $lot['org'],
                    $lot['direccion'],
                    $lot['AprobIni'],
                    $lot['renov'],
                    "PP",
                    $lot['servicio'],
                    $lot['limitacion'],
                    $lot['estado'],
                    $lot['extension'],
                    $lot['autom'],
                    $lot['maritim'],
                    $lot['TA'],
                    $lot['Ferrov'],
                    $lot['TH'],
                    $lot['totalmedios'],
                ];
            }

            $spreadsheet->addSheet($myWorkSheet);
            $myWorkSheet->getStyle('A1:R1')->applyFromArray($styleArray);
            $myWorkSheet->fromArray($list,null, 'A2', true);
        }
        // Llenamos los datos de la pagina principal
        $sheet->fromArray($estad,null, 'A2', true);

        // Auto-size columns for all worksheets
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            foreach ($worksheet->getColumnIterator() as $column) {
                $worksheet
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);
            }
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Crear archivo temporal en el sistema
        $fileName = 'Modelo1 '.$mcpio->getMunicipio().'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);
        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/mediosnac", name="estadistica_mediosnac")
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function datosMediosNac(){
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT `Provincias`,`provid` FROM `mediosxmcpio` group by `Provincias`";
        $stmt = $db->prepare($sql);
        $params = array();
        $stmt->execute($params);
        $resumen= $stmt->fetchAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen');
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
        foreach ($resumen as $prov) {
            $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $prov['Provincias']);
            //para cada prov creamos el encabezado para las paginas de las prov
            $myWorkSheet->getCell('A1')->setValue('Municipio');
            $myWorkSheet->getCell('B1')->setValue('Provincia');
            $myWorkSheet->getCell('C1')->setValue('Identificador');
            $myWorkSheet->getCell('D1')->setValue('Rama');
            $myWorkSheet->getCell('E1')->setValue('Combustible');
            $myWorkSheet->getCell('F1')->setValue('Marca');
            $myWorkSheet->getCell('G1')->setValue('Modelo');
            $myWorkSheet->getCell('H1')->setValue('Aseg');
            $myWorkSheet->getCell('I1')->setValue('tipo');
            $myWorkSheet->getCell('J1')->setValue('IC');
            $myWorkSheet->getCell('K1')->setValue('Potencia');
            $myWorkSheet->getCell('L1')->setValue('Pais de Aband');
            $myWorkSheet->getCell('M1')->setValue('Cap');
            $myWorkSheet->getCell('N1')->setValue('Año de Fab');
            $myWorkSheet->getCell('O1')->setValue('Serv Espec');
            $myWorkSheet->getCell('P1')->setValue('Basif');
            $myWorkSheet->getCell('Q1')->setValue('Servicio');
            $myWorkSheet->getCell('R1')->setValue('Folio Comp');
            //una vez tengamos el encabezado proseguimos con los datos
            $list = [];
            //obtenemos los datos del modelo para cada provincia en la interacion
            $medios = $this->DatosMedios(2,$prov['provid']);
            //organizamos los datos para imprimirlos al fichero xls
            foreach ($medios as $medio) {
               $list[] = [
                   $medio['Municipio'],
                   $medio['Provincias'],
                   $medio['nombre'],
                   $medio['Ramas'],
                   $medio['tipoComb'],
                   $medio['marca'],
                   $medio['modelo'],
                   $medio['aseguramiento'] ? 'Si' : 'No',
                   'tipomedio',
                   $medio['ind_consumo'],
                   $medio['potencia'],
                   $medio['pais_abanderamiento'],
                   $medio['cap'],
                   $medio['year_fab'],
                   $medio['servicios_especiales'],
                   $medio['nombreBasificacion'],
                   $medio['servicio'],
                   $medio['folio'],
                ];
          }
            $spreadsheet->addSheet($myWorkSheet);
            $myWorkSheet->getStyle('A1:R1')->applyFromArray($styleArray);
            $myWorkSheet->fromArray($list,null, 'A2', true);
            $myWorkSheet->getStyle('G2:G'.strval(count($list)+1))->applyFromArray($styleArray1);
            unset($list);
        }
        //Prueba para liberar ram
        unset($resumen);
        // Llenamos los datos de la pagina principal
//        $sheet->fromArray($estad,null, 'A2', true);

        // Auto-size columns for all worksheets
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $worksheet->setAutoFilter('A1:R1');
            foreach ($worksheet->getColumnIterator() as $column) {
                $worksheet
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);
            }
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $sheet->removeAutoFilter();
        // Crear archivo temporal en el sistema
        $fileName = 'mediosxprov.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);
        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/mediosprov/{prov}", name="estadistica_mediosprov")
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function datosMediosProv(Provincias $prov){
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT `Municipio`,`munid` FROM `mediosxmcpio` where `provid`=:provid group by `munid`";
        $stmt = $db->prepare($sql);
        $params = array('provid'=>$prov->getId());
        $stmt->execute($params);
        $resumen= $stmt->fetchAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen');
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
        foreach ($resumen as $mun) {
            $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $mun['Municipio']);
//            //para cada prov creamos el encabezado para las paginas de las prov
            $myWorkSheet->getCell('A1')->setValue('Municipio');
            $myWorkSheet->getCell('B1')->setValue('Provincia');
            $myWorkSheet->getCell('C1')->setValue('Identificador');
            $myWorkSheet->getCell('D1')->setValue('Rama');
            $myWorkSheet->getCell('E1')->setValue('Combustible');
            $myWorkSheet->getCell('F1')->setValue('Marca');
            $myWorkSheet->getCell('G1')->setValue('Modelo');
            $myWorkSheet->getCell('H1')->setValue('Aseg');
            $myWorkSheet->getCell('I1')->setValue('tipo');
            $myWorkSheet->getCell('J1')->setValue('IC');
            $myWorkSheet->getCell('K1')->setValue('Potencia');
            $myWorkSheet->getCell('L1')->setValue('Pais de Aband');
            $myWorkSheet->getCell('M1')->setValue('Cap');
            $myWorkSheet->getCell('N1')->setValue('Año de Fab');
            $myWorkSheet->getCell('O1')->setValue('Serv Espec');
            $myWorkSheet->getCell('P1')->setValue('Basif');
            $myWorkSheet->getCell('Q1')->setValue('Servicio');
            $myWorkSheet->getCell('R1')->setValue('Folio Comp');
//            //una vez tengamos el encabezado proseguimos con los datos
            $list = [];
            //obtenemos los datos del modelo para cada mcpio en la interacion
            $medios = $this->DatosMedios(1,$mun['munid']);
            //organizamos los datos para imprimirlos al fichero xls
            foreach ($medios as $medio) {
                $list[] = [
                    $medio['Municipio'],
                    $medio['Provincias'],
                    $medio['nombre'],
                    $medio['Ramas'],
                    $medio['tipoComb'],
                    $medio['marca'],
                    $medio['modelo'],
                    $medio['aseguramiento'] ? 'Si' : 'No',
                    $medio['Medios'],
                    $medio['ind_consumo'],
                    $medio['potencia'],
                    $medio['pais_abanderamiento'],
                    $medio['cap'],
                    $medio['year_fab'],
                    $medio['servicios_especiales'],
                    $medio['nombreBasificacion'],
                    $medio['servicio'],
                    $medio['folio'],
                ];
            }
            $spreadsheet->addSheet($myWorkSheet);
            $myWorkSheet->getStyle('A1:R1')->applyFromArray($styleArray);
            $myWorkSheet->fromArray($list,null, 'A2', true);
            $myWorkSheet->getStyle('G2:G'.strval(count($list)+1))->applyFromArray($styleArray1);
//
        }
        // Llenamos los datos de la pagina principal
//        $sheet->fromArray($estad,null, 'A2', true);

        // Auto-size columns for all worksheets
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $worksheet->setAutoFilter('A1:R1');
            foreach ($worksheet->getColumnIterator() as $column) {
                $worksheet
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);
            }
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $sheet->removeAutoFilter();
        // Crear archivo temporal en el sistema
        $fileName = 'mediosProv'.$prov->getProvincias().'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);
        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/mediosmcpal/{mcpio}", name="estadistica_mediosmcpl")
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function datosMediosMcpal(Municipios $mcpio){
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT `Municipio`,`munid` FROM `mediosxmcpio` where `munid`=:munid group by `munid`";
        $stmt = $db->prepare($sql);
        $params = array('munid'=>$mcpio->getId());
        $stmt->execute($params);
        $resumen= $stmt->fetchAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen');
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
        foreach ($resumen as $mun) {
            $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $mun['Municipio']);
//            //para el mcpio creamos el encabezado para las paginas
            $myWorkSheet->getCell('A1')->setValue('Municipio');
            $myWorkSheet->getCell('B1')->setValue('Provincia');
            $myWorkSheet->getCell('C1')->setValue('Identificador');
            $myWorkSheet->getCell('D1')->setValue('Rama');
            $myWorkSheet->getCell('E1')->setValue('Combustible');
            $myWorkSheet->getCell('F1')->setValue('Marca');
            $myWorkSheet->getCell('G1')->setValue('Modelo');
            $myWorkSheet->getCell('H1')->setValue('Aseg');
            $myWorkSheet->getCell('I1')->setValue('tipo');
            $myWorkSheet->getCell('J1')->setValue('IC');
            $myWorkSheet->getCell('K1')->setValue('Potencia');
            $myWorkSheet->getCell('L1')->setValue('Pais de Aband');
            $myWorkSheet->getCell('M1')->setValue('Cap');
            $myWorkSheet->getCell('N1')->setValue('Año de Fab');
            $myWorkSheet->getCell('O1')->setValue('Serv Espec');
            $myWorkSheet->getCell('P1')->setValue('Basif');
            $myWorkSheet->getCell('Q1')->setValue('Servicio');
            $myWorkSheet->getCell('R1')->setValue('Folio Comp');
//            //una vez tengamos el encabezado proseguimos con los datos
            $list = [];
            //obtenemos los datos del modelo para cada mcpio en la interacion
            $medios = $this->DatosMedios(1,$mun['munid']);
            //organizamos los datos para imprimirlos al fichero xls
            foreach ($medios as $medio) {
                $list[] = [
                    $medio['Municipio'],
                    $medio['Provincias'],
                    $medio['nombre'],
                    $medio['Ramas'],
                    $medio['tipoComb'],
                    $medio['marca'],
                    $medio['modelo'],
                    $medio['aseguramiento'] ? 'Si' : 'No',
                    $medio['Medios'],
                    $medio['ind_consumo'],
                    $medio['potencia'],
                    $medio['pais_abanderamiento'],
                    $medio['cap'],
                    $medio['year_fab'],
                    $medio['servicios_especiales'],
                    $medio['nombreBasificacion'],
                    $medio['servicio'],
                    $medio['folio'],
                ];
            }
            $spreadsheet->addSheet($myWorkSheet);
            $myWorkSheet->getStyle('A1:R1')->applyFromArray($styleArray);
            $myWorkSheet->fromArray($list,null, 'A2', true);
            $myWorkSheet->getStyle('G2:G'.strval(count($list)+1))->applyFromArray($styleArray1);
//
        }
        // Llenamos los datos de la pagina principal
//        $sheet->fromArray($estad,null, 'A2', true);

        // Auto-size columns for all worksheets
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $worksheet->setAutoFilter('A1:R1');
            foreach ($worksheet->getColumnIterator() as $column) {
                $worksheet
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);
            }
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $sheet->removeAutoFilter();
        // Crear archivo temporal en el sistema
        $fileName = 'mediosMcpio'.$mcpio->getMunicipio().'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);
        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    private function DatosModelo2(int $alcance,int $alcancevalor=null){
        switch ($alcance){
            case 1: $where = " where `mun`=:valor";
                break;
            case 2: $where = " where `prov`=:valor";
                break;
            default: $where = " where 1"; break;
        }
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT * FROM `datosmodelo2`";
        $stmt = $db->prepare($sql.$where);
        $params = array('valor'=>$alcancevalor);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * @Route("/modelo2nac", name="estadistica_modelo2nac")
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function datosModelo2Nac(){
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT `nombprov`,`prov` FROM `datosmodelo2` group by `nombprov`";
        $stmt = $db->prepare($sql);
        $params = array();
        $stmt->execute($params);
        $resumen= $stmt->fetchAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen');
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
        foreach ($resumen as $prov) {
            $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $prov['nombprov']);
            //para cada prov creamos el encabezado para las paginas de las prov
            $myWorkSheet->getCell('A1')->setValue('lot');
            $myWorkSheet->getCell('B1')->setValue('Prov');
            $myWorkSheet->getCell('C1')->setValue('Reeup');
            $myWorkSheet->getCell('D1')->setValue('Entidad');
            $myWorkSheet->getCell('E1')->setValue('Org');
            $myWorkSheet->getCell('F1')->setValue('FAprob');
            $myWorkSheet->getCell('G1')->setValue('FRenov');
            $myWorkSheet->getCell('H1')->setValue('Tipo');
            $myWorkSheet->getCell('I1')->setValue('Serv');
            $myWorkSheet->getCell('J1')->setValue('Limitacion');
            $myWorkSheet->getCell('K1')->setValue('Alcance');
            $myWorkSheet->getCell('L1')->setValue('Rama');
            $myWorkSheet->getCell('M1')->setValue('Estado');
            $myWorkSheet->getCell('N1')->setValue('Camiones');
            $myWorkSheet->getCell('O1')->setValue('Camionetas');
            $myWorkSheet->getCell('P1')->setValue('Autos');
            $myWorkSheet->getCell('Q1')->setValue('OtrosAut');
            $myWorkSheet->getCell('R1')->setValue('SubtAut');
            $myWorkSheet->getCell('S1')->setValue('SubtFC');
            $myWorkSheet->getCell('T1')->setValue('SubtMar');
            $myWorkSheet->getCell('U1')->setValue('SubtTA');
            $myWorkSheet->getCell('V1')->setValue('SubtTH');
            $myWorkSheet->getCell('W1')->setValue('TotalMedios');
            //una vez tengamos el encabezado proseguimos con los datos
            $list = [];
            //obtenemos los datos del modelo para cada provincia en la interacion
            $medios = $this->DatosModelo2(2,$prov['prov']);
            //organizamos los datos para imprimirlos al fichero xls
            foreach ($medios as $medio) {
                $list[] = [
                    $medio['lot'],
                    $medio['prov'],
                    $medio['CodReeup'],
                    $medio['nombreEntidad'],
                    $medio['organismo'],
                    $medio['faprob'],
                    $medio['frenov'],
                    $medio['tipolot'] ,
                    $medio['servicio'],
                    $medio['limitacion'],
                    $medio['ext'],
                    $medio['rama'],
                    $medio['estadolot'],
                    $medio['cam'],
                    $medio['camta'],
                    $medio['auto'],
                    $medio['otros'],
                    $medio['subtAut'],
                    $medio['subtFC'],
                    $medio['subtMar'],
                    $medio['subtTA'],
                    $medio['subtTH'],
                    $medio['total'],
                ];
            }
            $spreadsheet->addSheet($myWorkSheet);
            $myWorkSheet->getStyle('A1:W1')->applyFromArray($styleArray);
            $myWorkSheet->fromArray($list,null, 'A2', true);
//            $myWorkSheet->getStyle('G2:G'.strval(count($list)+1))->applyFromArray($styleArray1);

        }
        // Llenamos los datos de la pagina principal
//        $sheet->fromArray($estad,null, 'A2', true);

        // Auto-size columns for all worksheets
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $worksheet->setAutoFilter('A1:W1');
            foreach ($worksheet->getColumnIterator() as $column) {
                $worksheet
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);
            }
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $sheet->removeAutoFilter();
        // Crear archivo temporal en el sistema
        $fileName = 'Modelo2Nac.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);
        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/modelo2prov/{prov}",name="estadistica_modelo2prov")
     */
    public function datosModelo2Prov(Provincias $prov){
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT `nombmun`,`mun` FROM `datosmodelo2` where prov=:prov group by `mun`";
        $stmt = $db->prepare($sql);
        $params = array('prov'=>$prov->getId());
        $stmt->execute($params);
        $resumen= $stmt->fetchAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen');
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
        foreach ($resumen as $mun) {
            $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $mun['nombmun']);
            //para cada prov creamos el encabezado para las paginas de las prov
            $myWorkSheet->getCell('A1')->setValue('lot');
            $myWorkSheet->getCell('B1')->setValue('Prov');
            $myWorkSheet->getCell('C1')->setValue('Reeup');
            $myWorkSheet->getCell('D1')->setValue('Entidad');
            $myWorkSheet->getCell('E1')->setValue('Org');
            $myWorkSheet->getCell('F1')->setValue('FAprob');
            $myWorkSheet->getCell('G1')->setValue('FRenov');
            $myWorkSheet->getCell('H1')->setValue('Tipo');
            $myWorkSheet->getCell('I1')->setValue('Serv');
            $myWorkSheet->getCell('J1')->setValue('Limitacion');
            $myWorkSheet->getCell('K1')->setValue('Alcance');
            $myWorkSheet->getCell('L1')->setValue('Rama');
            $myWorkSheet->getCell('M1')->setValue('Estado');
            $myWorkSheet->getCell('N1')->setValue('Camiones');
            $myWorkSheet->getCell('O1')->setValue('Camionetas');
            $myWorkSheet->getCell('P1')->setValue('Autos');
            $myWorkSheet->getCell('Q1')->setValue('OtrosAut');
            $myWorkSheet->getCell('R1')->setValue('SubtAut');
            $myWorkSheet->getCell('S1')->setValue('SubtFC');
            $myWorkSheet->getCell('T1')->setValue('SubtMar');
            $myWorkSheet->getCell('U1')->setValue('SubtTA');
            $myWorkSheet->getCell('V1')->setValue('SubtTH');
            $myWorkSheet->getCell('W1')->setValue('TotalMedios');
            //una vez tengamos el encabezado proseguimos con los datos
            $list = [];
            //obtenemos los datos del modelo para cada provincia en la interacion
            $medios = $this->DatosModelo2(1,$mun['mun']);
            //organizamos los datos para imprimirlos al fichero xls
            foreach ($medios as $medio) {
                $list[] = [
                    $medio['lot'],
                    $medio['prov'],
                    $medio['CodReeup'],
                    $medio['nombreEntidad'],
                    $medio['organismo'],
                    $medio['faprob'],
                    $medio['frenov'],
                    $medio['tipolot'] ,
                    $medio['servicio'],
                    $medio['limitacion'],
                    $medio['ext'],
                    $medio['rama'],
                    $medio['estadolot'],
                    $medio['cam'],
                    $medio['camta'],
                    $medio['auto'],
                    $medio['otros'],
                    $medio['subtAut'],
                    $medio['subtFC'],
                    $medio['subtMar'],
                    $medio['subtTA'],
                    $medio['subtTH'],
                    $medio['total'],
                ];
            }
            $spreadsheet->addSheet($myWorkSheet);
            $myWorkSheet->getStyle('A1:W1')->applyFromArray($styleArray);
            $myWorkSheet->fromArray($list,null, 'A2', true);
//            $myWorkSheet->getStyle('G2:G'.strval(count($list)+1))->applyFromArray($styleArray1);

        }
        // Llenamos los datos de la pagina principal
//        $sheet->fromArray($estad,null, 'A2', true);

        // Auto-size columns for all worksheets
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $worksheet->setAutoFilter('A1:W1');
            foreach ($worksheet->getColumnIterator() as $column) {
                $worksheet
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);
            }
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $sheet->removeAutoFilter();
        // Crear archivo temporal en el sistema
        $fileName = 'Modelo2'.$prov->getProvincias().'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);
        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/modelo2mcpal/{mcpio}",name="estadistica_modelo2mcpal")
     */
    public function datosModelo2Mcpal(Municipios $mcpio){

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen');
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
        $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $mcpio->getMunicipio());
        //para cada prov creamos el encabezado para las paginas de las prov
        $myWorkSheet->getCell('A1')->setValue('lot');
        $myWorkSheet->getCell('B1')->setValue('Prov');
        $myWorkSheet->getCell('C1')->setValue('Reeup');
        $myWorkSheet->getCell('D1')->setValue('Entidad');
        $myWorkSheet->getCell('E1')->setValue('Org');
        $myWorkSheet->getCell('F1')->setValue('FAprob');
        $myWorkSheet->getCell('G1')->setValue('FRenov');
        $myWorkSheet->getCell('H1')->setValue('Tipo');
        $myWorkSheet->getCell('I1')->setValue('Serv');
        $myWorkSheet->getCell('J1')->setValue('Limitacion');
        $myWorkSheet->getCell('K1')->setValue('Alcance');
        $myWorkSheet->getCell('L1')->setValue('Rama');
        $myWorkSheet->getCell('M1')->setValue('Estado');
        $myWorkSheet->getCell('N1')->setValue('Camiones');
        $myWorkSheet->getCell('O1')->setValue('Camionetas');
        $myWorkSheet->getCell('P1')->setValue('Autos');
        $myWorkSheet->getCell('Q1')->setValue('OtrosAut');
        $myWorkSheet->getCell('R1')->setValue('SubtAut');
        $myWorkSheet->getCell('S1')->setValue('SubtFC');
        $myWorkSheet->getCell('T1')->setValue('SubtMar');
        $myWorkSheet->getCell('U1')->setValue('SubtTA');
        $myWorkSheet->getCell('V1')->setValue('SubtTH');
        $myWorkSheet->getCell('W1')->setValue('TotalMedios');
        //una vez tengamos el encabezado proseguimos con los datos
        $list = [];
        //obtenemos los datos del modelo para cada provincia en la interacion
        $medios = $this->DatosModelo2(1,$mcpio->getId());
        //organizamos los datos para imprimirlos al fichero xls
        foreach ($medios as $medio) {
            $list[] = [
                $medio['lot'],
                $medio['prov'],
                $medio['CodReeup'],
                $medio['nombreEntidad'],
                $medio['organismo'],
                $medio['faprob'],
                $medio['frenov'],
                $medio['tipolot'] ,
                $medio['servicio'],
                $medio['limitacion'],
                $medio['ext'],
                $medio['rama'],
                $medio['estadolot'],
                $medio['cam'],
                $medio['camta'],
                $medio['auto'],
                $medio['otros'],
                $medio['subtAut'],
                $medio['subtFC'],
                $medio['subtMar'],
                $medio['subtTA'],
                $medio['subtTH'],
                $medio['total'],
            ];
        }
        $spreadsheet->addSheet($myWorkSheet);
        $myWorkSheet->getStyle('A1:W1')->applyFromArray($styleArray);
        $myWorkSheet->fromArray($list,null, 'A2', true);
//            $myWorkSheet->getStyle('G2:G'.strval(count($list)+1))->applyFromArray($styleArray1);


        // Llenamos los datos de la pagina principal
//        $sheet->fromArray($estad,null, 'A2', true);

        // Auto-size columns for all worksheets
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $worksheet->setAutoFilter('A1:W1');
            foreach ($worksheet->getColumnIterator() as $column) {
                $worksheet
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);
            }
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $sheet->removeAutoFilter();
        // Crear archivo temporal en el sistema
        $fileName = 'Modelo2'.$mcpio->getMunicipio().'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);
        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    private function datosInstalaciones(int $alcance, int $alcancevalor=null){
        switch ($alcance){
            case 1: $where = " where `municipio_id`=:valor";
                break;
            case 2: $where = " where `provid`=:valor";
                break;
            default: $where = " where 1"; break;
        }
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT * FROM `datosinstalaciones`";
        $stmt = $db->prepare($sql.$where);
        $params = array('valor'=>$alcancevalor);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * @Route("/datosinstanac", name="estadistica_datosinstanac")
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function datosInstaNac(){
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT `provnomb`,`provid` FROM `datosinstalaciones` group by `provid`";
        $stmt = $db->prepare($sql);
        $params = array();
        $stmt->execute($params);
        $resumen= $stmt->fetchAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen');
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
        foreach ($resumen as $prov) {
            $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $prov['provnomb']);
            //para cada prov creamos el encabezado para las paginas de las prov
            $myWorkSheet->getCell('A1')->setValue('lot');
            $myWorkSheet->getCell('B1')->setValue('Entidad');
            $myWorkSheet->getCell('C1')->setValue('Organismo');
            $myWorkSheet->getCell('D1')->setValue('Tipo');
            $myWorkSheet->getCell('E1')->setValue('F. Venc');
            $myWorkSheet->getCell('F1')->setValue('Serv.Aprob');
            $myWorkSheet->getCell('G1')->setValue('Nombre Intalacion');
            $myWorkSheet->getCell('H1')->setValue('Municipio');
            $myWorkSheet->getCell('I1')->setValue('Direccion Inst');
            $myWorkSheet->getCell('J1')->setValue('Estado Comp');

            //una vez tengamos el encabezado proseguimos con los datos
            $list = [];
            //obtenemos los datos del modelo para cada provincia en la interacion
            $instalaciones = $this->datosInstalaciones(2,$prov['provid']);
            //organizamos los datos para imprimirlos al fichero xls
            foreach ($instalaciones as $inst) {
                $list[] = [
                    $inst['numlot'],
                    $inst['NomEntidad'],
                    $inst['org'],
                    $inst['tipodelot'],
                    $inst['fvencimiento'],
                    $inst['ServAuxConAnexo2'],
                    $inst['instanomb'],
                    $inst['mun'],
                    $inst['instadir'],
                    $inst['estadocomp'],
                ];
            }
            $spreadsheet->addSheet($myWorkSheet);
            $myWorkSheet->getStyle('A1:J1')->applyFromArray($styleArray);
            $myWorkSheet->fromArray($list,null, 'A2', true);
//            $myWorkSheet->getStyle('G2:G'.strval(count($list)+1))->applyFromArray($styleArray1);

        }
        // Llenamos los datos de la pagina principal
//        $sheet->fromArray($estad,null, 'A2', true);

        // Auto-size columns for all worksheets
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $worksheet->setAutoFilter('A1:J1');
            foreach ($worksheet->getColumnIterator() as $column) {
                $worksheet
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);
            }
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $sheet->removeAutoFilter();
        // Crear archivo temporal en el sistema
        $fileName = 'InstalacionesNac.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);
        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/datosinstaprov/{provincia}", name="estadistica_datosinstaprov")
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function datosInstaProv(Provincias $provincia){
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT `mun`,`municipio_id` FROM `datosinstalaciones` where provid=:prov group by `mun`";
        $stmt = $db->prepare($sql);
        $params = array('prov'=>$provincia->getId());
        $stmt->execute($params);
        $resumen= $stmt->fetchAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen');
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
        foreach ($resumen as $prov) {
            $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $prov['mun']);
            //para cada prov creamos el encabezado para las paginas de las prov
            $myWorkSheet->getCell('A1')->setValue('lot');
            $myWorkSheet->getCell('B1')->setValue('Entidad');
            $myWorkSheet->getCell('C1')->setValue('Organismo');
            $myWorkSheet->getCell('D1')->setValue('Tipo');
            $myWorkSheet->getCell('E1')->setValue('F. Venc');
            $myWorkSheet->getCell('F1')->setValue('Serv.Aprob');
            $myWorkSheet->getCell('G1')->setValue('Nombre Intalacion');
            $myWorkSheet->getCell('H1')->setValue('Municipio');
            $myWorkSheet->getCell('I1')->setValue('Direccion Inst');
            $myWorkSheet->getCell('J1')->setValue('Estado Comp');

            //una vez tengamos el encabezado proseguimos con los datos
            $list = [];
            //obtenemos los datos del modelo para cada provincia en la interacion
            $instalaciones = $this->datosInstalaciones(1,$prov['municipio_id']);
            //organizamos los datos para imprimirlos al fichero xls
            foreach ($instalaciones as $inst) {
                $list[] = [
                    $inst['numlot'],
                    $inst['NomEntidad'],
                    $inst['org'],
                    $inst['tipodelot'],
                    $inst['fvencimiento'],
                    $inst['ServAuxConAnexo2'],
                    $inst['instanomb'],
                    $inst['mun'],
                    $inst['instadir'],
                    $inst['estadocomp'],
                ];
            }
            $spreadsheet->addSheet($myWorkSheet);
            $myWorkSheet->getStyle('A1:J1')->applyFromArray($styleArray);
            $myWorkSheet->fromArray($list,null, 'A2', true);
//            $myWorkSheet->getStyle('G2:G'.strval(count($list)+1))->applyFromArray($styleArray1);

        }
        // Llenamos los datos de la pagina principal
//        $sheet->fromArray($estad,null, 'A2', true);

        // Auto-size columns for all worksheets
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $worksheet->setAutoFilter('A1:J1');
            foreach ($worksheet->getColumnIterator() as $column) {
                $worksheet
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);
            }
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $sheet->removeAutoFilter();
        // Crear archivo temporal en el sistema
        $fileName = 'Instalaciones'.$provincia->getProvincias().'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);
        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/datosinstamcpal/{mcpio}", name="estadistica_datosinstamcpal")
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function datosInstaMcpio(Municipios $mcpio){
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT `mun`,`municipio_id` FROM `datosinstalaciones` where municipio_id=:mcpio group by `mun`";
        $stmt = $db->prepare($sql);
        $params = array('mcpio'=>$mcpio->getId());
        $stmt->execute($params);
        $resumen= $stmt->fetchAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen');
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
        foreach ($resumen as $prov) {
            $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $prov['mun']);
            //para cada prov creamos el encabezado para las paginas de las prov
            $myWorkSheet->getCell('A1')->setValue('lot');
            $myWorkSheet->getCell('B1')->setValue('Entidad');
            $myWorkSheet->getCell('C1')->setValue('Organismo');
            $myWorkSheet->getCell('D1')->setValue('Tipo');
            $myWorkSheet->getCell('E1')->setValue('F. Venc');
            $myWorkSheet->getCell('F1')->setValue('Serv.Aprob');
            $myWorkSheet->getCell('G1')->setValue('Nombre Intalacion');
            $myWorkSheet->getCell('H1')->setValue('Municipio');
            $myWorkSheet->getCell('I1')->setValue('Direccion Inst');
            $myWorkSheet->getCell('J1')->setValue('Estado Comp');

            //una vez tengamos el encabezado proseguimos con los datos
            $list = [];
            //obtenemos los datos del modelo para cada provincia en la interacion
            $instalaciones = $this->datosInstalaciones(1,$prov['municipio_id']);
            //organizamos los datos para imprimirlos al fichero xls
            foreach ($instalaciones as $inst) {
                $list[] = [
                    $inst['numlot'],
                    $inst['NomEntidad'],
                    $inst['org'],
                    $inst['tipodelot'],
                    $inst['fvencimiento'],
                    $inst['ServAuxConAnexo2'],
                    $inst['instanomb'],
                    $inst['mun'],
                    $inst['instadir'],
                    $inst['estadocomp'],
                ];
            }
            $spreadsheet->addSheet($myWorkSheet);
            $myWorkSheet->getStyle('A1:J1')->applyFromArray($styleArray);
            $myWorkSheet->fromArray($list,null, 'A2', true);
//            $myWorkSheet->getStyle('G2:G'.strval(count($list)+1))->applyFromArray($styleArray1);

        }
        // Llenamos los datos de la pagina principal
//        $sheet->fromArray($estad,null, 'A2', true);

        // Auto-size columns for all worksheets
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $worksheet->setAutoFilter('A1:J1');
            foreach ($worksheet->getColumnIterator() as $column) {
                $worksheet
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);
            }
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $sheet->removeAutoFilter();
        // Crear archivo temporal en el sistema
        $fileName = 'Instalaciones'.$mcpio->getMunicipio().'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Guardar el archivo de excel en el directorio temporal del sistema
        $writer->save($temp_file);
        // Retornar excel como descarga
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/nacional", name="estadistica_nacional")
     */
    public function estadisticaNacional(){
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT * FROM `lotsxestadoxprov`";
        $stmt = $db->prepare($sql);
        $params = array();
        $stmt->execute($params);
        $vigentes=$stmt->fetchAll();
        return $this->render("estadistica/nacional.html.twig",[
            'vigentes'=>$vigentes,
        ]);
    }

    /**
     * @Route("/json/lotsxestado/nac")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getjsonlotsxestado(){
        $db = $this->getDoctrine()->getConnection();
        $sql = "SELECT `provinciaid`,`provincia`,`vigentes` FROM `lotsxestadoxprov`";
        $stmt = $db->prepare($sql);
        $params = array();
        $stmt->execute($params);
        $datosEstLot=$stmt->fetchAll();

        return $this->json($datosEstLot);
    }
}
