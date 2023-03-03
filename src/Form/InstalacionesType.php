<?php

namespace App\Form;

use App\Entity\Instalaciones;
use App\Form\DataTransformer\ArrayToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InstalacionesType extends AbstractType
{
    private $transformer;
    private $municipios;
    private $servicios;
    private $aseguramiento;
    public function __construct(ArrayToStringTransformer $transformer)
    {
        $this->transformer = $transformer;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->municipios = $options['municipios'];
        $this->servicios = $options['servicios'];
        $this->aseguramiento = $options['aseguramiento'];
        $builder
            ->add('nombre')
            ->add('direccion')
            ->add('aseguramiento',null,[
                'attr'=>['checked'=>$this->aseguramiento, 'required'=>$this->aseguramiento],
            ])
            ->add('lot',null,[
                'attr'=>['class'=>'mi-selector']
            ])
            ->add('municipio',null,[
                'choices'=>$this->municipios,
                'attr'=>['class'=>'mi-selector']
            ])
            ->add('servAuxCon', null,[
//                'attr'=>['class'=>'mi-selector'],
                'choices'=>$this->servicios,
                'label'=>'Servicio Auxiliar o Conexo'
            ])
            ->add('servicioshtml', ChoiceType::class,[
//                'attr'=>['class'=>'mi-selector'],
                'choices'=>$this->servicios,
                'multiple'=>'true',
                'expanded'=>'true',
                'choice_value' => ChoiceList::value($this, 'id'),
                'choice_label' => function ($choice, $key, $value) {
                    if (true === $choice) {
                        return 'Definitely!';
                    }
                    return ($choice->getServAuxCon());
                },
                'label'=>'Servicio(s) a prestar',
                'mapped' => false,
            ])
            ->add('servicios1')
            ->add('servvisual')
        ;
        $builder->get('servicios1')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Instalaciones::class,
            'municipios' => null,
            'servicios' => null,
            'aseguramiento'=> false,
        ]);
    }
}
