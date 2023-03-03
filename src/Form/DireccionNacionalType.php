<?php

namespace App\Form;

use App\Entity\DireccionNacional;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DireccionNacionalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('direccion')
            ->add('telefonos')
            ->add('director')
            ->add('subdirLot')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DireccionNacional::class,
        ]);
    }
}
