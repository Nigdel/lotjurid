<?php

namespace App\Form;

use App\Entity\OficinaMcpal;
//use App\Form\DataTransformer\ArrayToStringTransformer;
use Symfony\Component\Form\AbstractType;
//use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OficinaMcpalType extends AbstractType
{
//    private $transformer;
//
//    public function __construct(ArrayToStringTransformer $transformer)
//    {
//        $this->transformer = $transformer;
//    }

    private $funcionarios;
    private $municipios;
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->funcionarios = $options['funcionarios'];
        $this->municipios = $options['municipios'];
        $builder
//            ->add('funcionarios')
            ->add('municipio',null,[
                'choices'=>$this->municipios,
                'label' => 'Municipio'
            ])
            ->add('direccion')
            ->add('telefono')
            ->add('email')
            ->add('director',null,[
                'choices'=>$this->funcionarios,
                'label' => 'Jefe de Oficina',
                'attr'=>['class'=>'mi-selector']
            ])

            ->add('firmacomp',null,[
                'choices'=>$this->funcionarios,
                'label' => 'Firma Comprobantes',
                'attr'=>['class'=>'mi-selector']
            ])
            ->add('firmalot',null,[
                'choices'=>$this->funcionarios,
                'label'=>'Firma las Lots',
                'attr'=>['class'=>'mi-selector']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OficinaMcpal::class,
            'funcionarios'=> null,
            'municipios'=>null,
        ]);
    }
}
