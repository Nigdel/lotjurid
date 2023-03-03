<?php

namespace App\Form;

use App\Entity\Basificacion;
use App\Entity\Municipios;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BasificacionType extends AbstractType
{
    private $municipios;
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->municipios= $options['municipios'];
        $builder
            ->add('idlbasiam', HiddenType::class)
            ->add('nombrelb', null,[
                'label'=>'Nombre'
            ])
            ->add('direccion')
            ->add('idlicencia')
            ->add('idmun',null,[
//                'preferred_choices' =>$mcpios,
        'choices'=>$this->municipios,
        'label' => 'Municipio',
        'required'=> true,
        'attr'=>['class'=>'mi-selector']
//        'choice_attr' => function(?Municipios $munc) {
//            if($munc->getProvinciaid()->getId()!= 10)
//                return ['class'=> 'invisible'];
//            else
//                return [];
//                    return $munc ? ['class' => 'category_'.strtolower($munc->getProvinciaid())] : [];
//        },
//        'attr'=>['class'=>'form-control','disabled'=>''],
    ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Basificacion::class,
            'municipios' => null
        ]);
    }
}
