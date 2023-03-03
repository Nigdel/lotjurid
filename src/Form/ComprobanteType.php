<?php

namespace App\Form;

use App\Entity\Comprobante;
use App\Form\DataTransformer\DateTimeToStringTransformer;
use \Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComprobanteType extends AbstractType
{
    private $transformer;
    private $exts;
    public function __construct(DateTimeToStringTransformer $transformer)
    {
        $this->transformer = $transformer;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->exts = $options['exts'];
        $builder
            ->add('femitido',TextType::class, ['required' => true,
            'label'=>"F. Emitido",
            'attr'=>['class'=>'datetimepicker4'],
            ])
            ->add('fimpreso',TextType::class, ['required' => false,
                'label'=>"F. Impreso",
                'attr'=>['class'=>'datetimepicker4'],
            ])
            ->add('fentrega',TextType::class, ['required' => false,
                'label'=>"F. Entrega",
                'attr'=>['class'=>'datetimepicker4'],
            ])
            ->add('fcancel',TextType::class, ['required' => false,
                'label'=>"F. Cancelacion",
                'attr'=>['class'=>'datetimepicker4'],
            ])
            ->add('sincostp',null,[
                'label'=>'Sin Costo',
            ])
            ->add('duplicado')
            ->add('importe')
            ->add('medio')
            ->add('folio')
            ->add('lot')
            ->add('extension',null,[
                'choices'=>$this->exts,
                'label' => 'Extension',
                'required'=> true,
            ])
            ->add('estadoComp')
        ;
        $builder->get('femitido')
            ->addModelTransformer($this->transformer);
        $builder->get('fimpreso')
            ->addModelTransformer($this->transformer);
        $builder->get('fentrega')
            ->addModelTransformer($this->transformer);
        $builder->get('fcancel')
            ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comprobante::class,
            'exts' => null
        ]);
    }
}
