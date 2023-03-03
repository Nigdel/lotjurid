<?php

namespace App\Form;

use App\Entity\Foliocomprobantes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FoliocomprobantesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('licencia')
            ->add('chapa')
            ->add('colortipo')
            ->add('fechaemitido')
            ->add('fechaentrega')
            ->add('estadomodelolot')
            ->add('fechadecancelacion')
            ->add('causadecancelacion')
            ->add('fechaimpresion')
            ->add('sincosto')
            ->add('duplicado')
            ->add('moneda')
            ->add('extension')
            ->add('importe')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Foliocomprobantes::class,
        ]);
    }
}
