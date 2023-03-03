<?php

namespace App\Form;

use App\Entity\Municipios;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MunicipiosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sGuid')
            ->add('codigo')
            ->add('municipio')
            ->add('provinciaid')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Municipios::class,
        ]);
    }
}
