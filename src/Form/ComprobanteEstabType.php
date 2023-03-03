<?php

namespace App\Form;

use App\Entity\Compestab;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComprobanteEstabType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('folio')
            ->add('femitido')
            ->add('fimpreso')
            ->add('fentrega')
            ->add('fcancel')
            ->add('sincostp')
            ->add('duplicado')
            ->add('importe')
            ->add('cSuspencion')
            ->add('finicioSusp')
            ->add('ffinSusp')
            ->add('fvencimiento')
            ->add('estadoComp')
            ->add('cCancel')
            ->add('instalacion')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Compestab::class,
        ]);
    }
}
