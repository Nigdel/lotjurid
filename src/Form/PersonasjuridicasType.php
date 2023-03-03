<?php

namespace App\Form;

use App\Entity\Personasjuridicas;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonasjuridicasType extends AbstractType
{
    private $municipios;
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->municipios= $options['municipios'];
        $builder
            ->add('id',null,[
                'label'=>'Expediente',
            ])
            ->add('codreeup',null,[
                'label'=>'Reeup',
             ])
            ->add('nomentidad',null,[
                'label'=>'Nombre de la Entidad',
            ])
            ->add('direccion')
            ->add('telefono')
            ->add('email')

            ->add('actividad')
            ->add('rama')
            ->add('subrama')
            ->add('nocontribuyente',null,[
                'label'=>'Num Contribuyente',
            ])
            ->add('representante')
            ->add('idorga',null,[
                'label'=>'Organismo',
                'attr'=>['class'=>'mi-selector']
            ])
            ->add('idmunicipio',null,[
                'label'=>'Municipio',
                'choices'=>$this->municipios,
                'required'=> true,
                'attr'=>['class'=>'mi-selector']
            ])
            ->add('tipoEmpresa',null,[
                'required'=>true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Personasjuridicas::class,
            'municipios'=>null,
        ]);
    }
}
