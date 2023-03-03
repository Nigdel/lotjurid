<?php

namespace App\Controller;

use App\Entity\Municipios;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\MensajeRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/chgpassw", name="user_chgpassw")
     */
    public function changePassw(Request $reques,UserPasswordEncoderInterface $passwordEncoder){
        $usuario = $this->getUser();
        $form = $this->createFormBuilder()
            ->add('password',PasswordType::class,[
                'required' => 'true',
                'label'=>'Contraseña Actual'
            ])
            ->add('newpassword',RepeatedType::class,[
                'type'=> PasswordType::class,
                'required' => 'true',
                'first_options'=>['label'=>'Nueva Contraseña'],
                'second_options'=>['label'=>'Confirme Contraseña'],
                'mapped' => false,
            ])
            ->getForm();
        $form->handleRequest($reques);
        if ($form->isSubmitted() && $form->isValid()) {
            $newpass = $passwordEncoder->encodePassword($usuario,$form['newpassword']->getData()) ;
            if($passwordEncoder->isPasswordValid($usuario,$form['password']->getData())){
                var_dump($newpass);
                $usuario->setPassword($newpass);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($usuario);
                $entityManager->flush();
                $this->addFlash('success',"Se actualizó la contraseña correctamente");
                return $this->redirectToRoute('user_show',['id'=>$usuario->getid()]);
            }
            else{
                            $form->addError(new FormError(
                                'La contraseña Actual no es correcta'
                            ));

                $this->addFlash('error',"La contraseña Actual no es correcta");
                return $this->render('user/changepassw.html.twig',[
                    'form'=>$form->createView(),
                    'user'=>$usuario,
                ]);
            }

        }
        return $this->render('user/changepassw.html.twig',[
            'form'=>$form->createView(),
            'user'=>$usuario,
        ]);
    }

    /**
     * @Route("/miperfil/edit", name="userperf_edit", methods={"GET","POST"})
     */
    public function editperfil(Request $request): Response
    {
        $user= $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/mismensajes", name="user_msjs")
     */
    public function misMsjes(){
        $user = $this->getUser();
        return $this->render('mensaje/index.html.twig',[
            'mensajes'=>$user->getMismensajes(),
        ]);
    }

    /**
     * @Route("/resetpassw", name="user_resetpassw")
     */
    public function resetpassw(Request $request, UserPasswordEncoderInterface $passwordEncoder){
        $form = $this->createFormBuilder()
            ->add('email')

            ->add('Reset',SubmitType::class,[
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
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository(User::class)->findOneBy(['email'=>$data['email']]);
                $user->setPassword(
                    $passwordEncoder->encodePassword($user,'123')
                );
                $em->persist($user);
                $em->flush();
                $this->addFlash('success','Se ha reseteado el password al usuario: '.$user);
               return $this->redirectToRoute('user_show',[
                    'id' => $user->getId(),
                ]);
//                $this->redirect($this->generateUrl('user_index'));
            }
        }
        return $this->render('user/resetpasswd.html.twig', [
            'form'=> $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }


}
