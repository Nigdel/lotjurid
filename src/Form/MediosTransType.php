<?php

namespace App\Form;

use App\Entity\MediosTrans;
use App\Form\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediosTransType extends AbstractType
{
   private $transformer;
   private $tipos;
    public function __construct(DateTimeToStringTransformer $transformer)
    {
        $this->transformer = $transformer;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->tipos = $options['tipos'];
        $builder
            ->add('nombre',null,[
                'label'=>'Identificador',
            ])
            ->add('basificacionObj',null,[
                'label'=>'Basificacion'
            ])
            ->add('tipoMedio',null,[
                'choices'=>$this->tipos,'required'=> true,
            ])
            ->add('yearFab',null,[
                'label'=>'Año de Fab.',
            ])
            ->add('cap',null,[
                'label'=>'Capacidad (Carga)',
            ])
            ->add('umcap',null,[
                'label'=>'Unidad de Medida Cap'
            ])
            ->add('aseguramiento')
            ->add('marca')
            ->add('modelo')
            ->add('estadoMedio',null,[
                'label'=>'Estado',
                'required'=> true,
            ])
            ->add('serviciosEspeciales',null,[
                'label'=>'Serv. Especiales',
            ])
            ->add('tipoCombustible',null,[
                'label'=>'Combustible','required'=> true,
            ])
            ->add('indConsumo',null,[
                'label'=>'Indice de Consumo',
            ])
            ->add('potencia',null,[
                'label'=>'Potencia',
            ])
            ->add('tipoPropiedad',null,[
                'label'=>'Propiedad',
            ])
            ->add('rama')
            ->add('numRevTecnica',null,[
                'label'=>'Revisión Técnica',
            ])
            ->add('vencRevTecnica',TextType::class,[
                'label'=>'Vencimiento RT',
                'attr'=>['class'=>'datetimepicker4']
            ])
            ->add('paisAbanderamiento',null,[
                'label'=>'Pais de Abanderamiento',
            ])
            ->add('idTipo',null,[
                'label'=>'Tipo',
            ])
            ->add('servicio')
            ->add('capPDepie')
            ->add('capPSentados')
            ->add('tipoCamion')
        ;
        $builder->get('vencRevTecnica')
            ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MediosTrans::class,
            'tipos'=>null,
        ]);
    }
}
