<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User1Type;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\RandomString;

/**
 * @Route("/admin/user")
 */
class UserController extends AbstractController
{
    /**
    * @var ValidatorInterface
    */
    private $validator;

    /**
    * @var RandomString
    */
    private $random_string;

    /**
    * @var Security
    */
    private $security;

    /**
    * @var User
    */
    private $user;

    public function __construct(ValidatorInterface $validator, RandomString $random_string, Security $security)
    {
        $this->validator = $validator;
        $this->random_string = $random_string;
        $this->security = $security;
        $this->user = $this->security->getUser();
    }

    /**
     * @Route("/", name="admin_user_index", methods="GET")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager()->getRepository(User::class);
        return $this->render('user/index.html.twig', ['users' => $em->findAll()]);
    }

    /**
     * @Route("/new", name="admin_user_new", methods="GET|POST")
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(User1Type::class, $user);
        $form->handleRequest($request);
        
        $errors = $this->validator->validate($user);

        if ($form->isSubmitted() && $form->isValid()) {
            //correctly encode password
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'errors' => $errors,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_show", methods="GET")
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/{id}/edit", name="admin_user_edit", methods="GET|POST")
     */
    public function edit(Request $request, UserPasswordEncoderInterface $passwordEncoder, User $user): Response
    {
        $form = $this->createForm(User1Type::class, $user);
        $form->handleRequest($request);

        $errors = $this->validator->validate($user);

        if ($form->isSubmitted() && $form->isValid()) {
            //correctly encode password
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_user_edit', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'errors' => $errors,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_delete", methods="DELETE")
     */
    public function delete(Request $request, User $user): Response
    {
        if( $this->user->hasRole('ROLE_ADMIN') )
        {
            if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($user);
                $em->flush();
            }
        }
        else{
            $this->addFlash(
                'notice',
                'You can\'t delete a user!'
            );
        }

        return $this->redirectToRoute('admin_user_index');
    }
}
