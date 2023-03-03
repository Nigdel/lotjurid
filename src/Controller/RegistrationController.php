<?php

namespace App\Controller;

use App\Entity\DireccionProvincial;
use App\Entity\Municipios;
use App\Entity\OficinaMcpal;
use App\Entity\User;
use function PHPSTORM_META\type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createFormBuilder()
            ->add('email')
            ->add('password',RepeatedType::class,[
                'type'=> PasswordType::class,
                'required' => 'true',
                'first_options'=>['label'=>'Password'],
                'second_options'=>['label'=>'Confirme Password']
            ])
            ->add('municipio',EntityType::class,[
                'class'=>Municipios::class,
                'label' => 'Municipio',
                'attr'=>['class'=>'mi-selector']
            ])
            ->add('Crear',SubmitType::class,[
                'attr'=>[
                    'class'=>'btn btn-success'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()){
            if($form->isValid()){
                $data = $form->getData();
//                dump($data);
                $user = new User();
                $user->setEmail($data['email']);
                $user->setPassword(
                    $passwordEncoder->encodePassword($user,$data['password'])
                );
                $user->setMunicipio($data['municipio']);
                $em = $this->getDoctrine()->getManager();

                $em->persist($user);
                $em->flush();

                $this->redirect($this->generateUrl('app_login'));
            }
        }
        return $this->render('registration/index.html.twig', [
            'form'=> $form->createView()
        ]);
    }

    /**
     * @Route("/om/{id}/RegisterNewFunc",name="ofic_mcpal_registerFunc")
     */
    public function registerNewFunc(Request $request, UserPasswordEncoderInterface $passwordEncoder,OficinaMcpal $oficinaMcpal){
       $nuevoF= new User();
       $nuevoF->setOficinaMcpal($oficinaMcpal);
       $nuevoF->setMunicipio($oficinaMcpal->getMunicipio());
       $nuevoF->setRoles(["ROLE_USER","ROLE_TRAMITADOR"]);

        $form = $this->createFormBuilder($nuevoF)
            ->add('email')
            ->add('password',RepeatedType::class,[
                'type'=> PasswordType::class,
                'required' => 'true',
                'first_options'=>['label'=>'Password'],
                'second_options'=>['label'=>'Confirme Password']
            ])

            ->add('nombreApellidos',null,[
                'required'=>'true'
            ])
            ->add('Crear',SubmitType::class,[
                'attr'=>[
                    'class'=>'btn btn-success'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()){
            if($form->isValid()){

                $data = $form->getData();
//                dump($data);
//                dump($nuevoF);
                $nuevoF->setPassword(
                    $passwordEncoder->encodePassword($nuevoF,$data->getPassword())
                );
                $em = $this->getDoctrine()->getManager();
                $em->persist($nuevoF);
                $em->flush();
               return $this->redirect($this->generateUrl('oficina_mcpal_show',['id'=>$oficinaMcpal->getId()]));
            }
        }
        return $this->render('registration/index.html.twig', [
            'form'=> $form->createView()
        ]);
    }

    /**
     * @Route("/dp/{id}/RegisterNewFunc",name="direccion_provincial_registerFunc")
     */
    public function registerFuncDP(Request $request, UserPasswordEncoderInterface $passwordEncoder,DireccionProvincial $dp){
        $nuevoF= new User();
        $nuevoF->setDireccionProvincial($dp);
       // $nuevoF->setMunicipio($dp->getMunicipio());
        $nuevoF->setRoles(["ROLE_USER","ROLE_TRAMITADOR"]);

        $form = $this->createFormBuilder($nuevoF)
            ->add('email')
            ->add('password',RepeatedType::class,[
                'type'=> PasswordType::class,
                'required' => 'true',
                'first_options'=>['label'=>'Password'],
                'second_options'=>['label'=>'Confirme Password']
            ])
            ->add('municipio',EntityType::class,[
                'class'=>Municipios::class,
                'attr'=>['class'=>'mi-selector']
            ])
//            ->add('oficinaMcpal',null,[
//                'attr'=>['disabled'=>'true']
//            ])
            ->add('nombreApellidos',null,[
                'required'=>'true'
            ])
            ->add('cargo')
            ->add('Crear',SubmitType::class,[
                'attr'=>[
                    'class'=>'btn btn-success'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()){
            if($form->isValid()){

                $data = $form->getData();
//                dump($data);
//                dump($nuevoF);
                $nuevoF->setPassword(
                    $passwordEncoder->encodePassword($nuevoF,$data->getPassword())
                );
                $em = $this->getDoctrine()->getManager();
                $em->persist($nuevoF);
                $em->flush();
                return $this->redirect($this->generateUrl('direccion_provincial_show',['id'=>$dp->getId()]));
            }
        }
        return $this->render('registration/index.html.twig', [
            'form'=> $form->createView()
        ]);
    }



}
