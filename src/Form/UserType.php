<?php

namespace App\Form;

use App\Entity\User;
use App\Form\DataTransformer\ArrayToStringTransformer;
use App\Form\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    private $transformer;
    private $transformer2;
    public function __construct(ArrayToStringTransformer $transformer, DateTimeToStringTransformer $transformer2)
    {
        $this->transformer = $transformer;
        $this->transformer2 = $transformer2;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('roles',TextType::class)
//            ->add('password',RepeatedType::class,[
//                'type'=> PasswordType::class,
//                'required' => 'true',
//                'first_options'=>['label'=>'Nuevo Password'],
//                'second_options'=>['label'=>'Confirme Nuevo Password']
//            ])
            ->add('nombreApellidos',null,[
                'label'=>'Nombre y Apellidos',
            ])
            ->add('carnetFuncionario')
            ->add('fechaContratacion',TextType::class, ['required' => false,
                'label'=>"Fecha de Contratacion",
                'attr'=>['class'=>'datetimepicker4'],
            ])
            ->add('nivelAcceso')
            ->add('municipio',null,[
                'label' => 'Municipio',
                'attr'=>['class'=>'mi-selector']
            ])
            ->add('cargo')
        ;
        $builder->get('roles')
            ->addModelTransformer($this->transformer);
        $builder->get('fechaContratacion')
            ->addModelTransformer($this->transformer2);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
