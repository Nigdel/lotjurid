<?php

namespace App\Form;

use App\Entity\Lotjuridicas;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\DataTransformer\DateTimeToStringTransformer;

class LotjuridicasType extends AbstractType
{
    private $transformer;
    public function __construct(DateTimeToStringTransformer $transformer)
    {
        $this->transformer = $transformer;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id',null,['label'=>"Expediente"])
            ->add('fechasolicitud',TextType::class,[
                'label'=>"Solicitada",
                'attr'=>['class'=>'datetimepicker4'],
            ])
            ->add('presentada',null,['label'=>"Presentada"])
            ->add('aprobada',null,['label'=>"Aprobada"])
            ->add('fechaaprobacion',TextType::class, ['required' => false,
                        'label'=>"F. Aprobada",
                        'attr'=>['class'=>'datetimepicker4'],
            ])
            ->add('fechaemision',TextType::class, ['required' => false,
                        'label'=>"F. Emitida",
                        'attr'=>['class'=>'datetimepicker4'],
            ])
            ->add('fechaentrega',TextType::class, ['required' => false,
                        'label'=>"F. Entrega",
                        'attr'=>['class'=>'datetimepicker4'],
                ])
            ->add('limitacion')
            ->add('tpomedioamparado')
            ->add('duracion')
            ->add('fechadecancelacion',TextType::class, [
                        'required' => false,
                        'label'=>"F. Cancelacion",
                        'attr'=>['class'=>'datetimepicker4'],
                ])
            ->add('fecharenov',TextType::class, ['required' => false,
                        'label'=>"F. Renovacion"
                ])
//            ->add('posteada')
            ->add('mediatarifa')
            ->add('numfolio')
            ->add('fechaaprobinicial',TextType::class, ['required' => false,
                        'label'=>"F. Aprob. Inic."
                ])
            ->add('tiposolicitud',null,['label'=>"Tipo de Solicitud"])
            ->add('causadecancelacion',null,[
                'label'=>"Causa de Cancelacion"
            ])
            ->add('dictamen',null,[
                'attr'=>['class'=>'col'],
                'required'=>true,
            ])
            ->add('c_negacion',null,['label'=>"Causa de Negacion"])
            ->add('prorrogadoendias')
            ->add('fechadedestruccion',TextType::class, ['required' => false,
                        'label'=>"F. Destruccion"
                ])
            ->add('duplicado')
            ->add('identidad',null,['label'=>"Entidad"])
            ->add('idtramitador',null,['label'=>"Tramitada Por"])
            ->add('idaprueba',null,['label'=>"Aprobada Por"])
            ->add('idtipo',null,['label'=>"Tipo",'required'=>true])
            ->add('idservicio',null,['label'=>"Servicio",'required'=>true])
            ->add('servicioamparado',null,['label'=>"Serv Amparado",'required'=>true])
            ->add('idextension',null,['label'=>"Extension",'required'=>true])
            ->add('idrama',null,['label'=>"Rama",'required'=>true])
            ->add('idestado',null,['label'=>'Estado'])
            ->add('importe',null,['attr'=>['readonly'=> true]]);
        ;
        $builder->get('fechasolicitud')
            ->addModelTransformer($this->transformer);
        $builder->get('fechaemision')
        ->addModelTransformer($this->transformer);
        $builder->get('fechaaprobacion')
            ->addModelTransformer($this->transformer);
        $builder->get('fechaentrega')
            ->addModelTransformer($this->transformer);
        $builder->get('fechadecancelacion')
            ->addModelTransformer($this->transformer);
        $builder->get('fechaaprobinicial')
        ->addModelTransformer($this->transformer);
        $builder->get('fechadedestruccion')
        ->addModelTransformer($this->transformer);
        $builder->get('fecharenov')
        ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lotjuridicas::class,
        ]);
    }
}
