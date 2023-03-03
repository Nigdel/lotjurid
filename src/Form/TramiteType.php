<?php

namespace App\Form;

use App\Entity\Tramite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TramiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha')
            ->add('Iporigen')
            ->add('observaciones')
            ->add('usuario')
            ->add('tipotramite')
            ->add('lot')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tramite::class,
        ]);
    }
}
