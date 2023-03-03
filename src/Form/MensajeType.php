<?php

namespace App\Form;

use App\Entity\Mensaje;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MensajeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('asunto')
            ->add('texto')
            ->add('envia')
            ->add('recibe',null,[
                'label'=>'Para:',
                'attr'=>['class'=>'mi-selector'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mensaje::class,
        ]);
    }
}
