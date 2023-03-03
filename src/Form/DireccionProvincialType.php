<?php

namespace App\Form;

use App\Entity\DireccionProvincial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DireccionProvincialType extends AbstractType
{
    private $funcionarios;
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->funcionarios= $options['funcionarios'];
        $builder
            ->add('direccion')
            ->add('telefonos')
            ->add('provincia')
            ->add('directorProvincial', null,[
                'choices'=>$this->funcionarios,
                'label' => 'Director Provincial',
            ])
            ->add('subdirLot', null,[
                'choices'=>$this->funcionarios,
                'label' => 'Subdirector Lot',
            ])
            ->add('subdirGral', null,[
                'choices'=>$this->funcionarios,
                'label' => 'Sub Dir General',
            ])
            ->add('firmalot', null,[
                'choices'=>$this->funcionarios,
                'label' => 'Firma las Lots',
            ])
            ->add('firmacomp', null,[
                'choices'=>$this->funcionarios,
                'label' => 'Firma los Comprobantes',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DireccionProvincial::class,
            'funcionarios'=> null,
        ]);
    }
}
