<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User1Type;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Security;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;


use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\RandomString;
use App\Service\SSP;

/**
 * @Route("/datatable/user")
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


    private $db_host;
    private $db_name;
    private $db_user;
    private $db_password;

    public function __construct(ValidatorInterface $validator, RandomString $random_string, Security $security, $db_host, $db_name, $db_user, $db_password)
    {
        $this->validator = $validator;
        $this->random_string = $random_string;
        $this->security = $security;
        $this->user = $this->security->getUser();
        $this->db_host = $db_host;
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_password = $db_password;
    }

    /**
     * @Route("/", name="admin_user_index", methods="GET")
     */
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager()->getRepository(User::class);
        $users = $em->findAll();

        /*$encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer(), new DateTimeNormalizer());
        $serializer = new Serializer($normalizers, $encoders);*/
        $fields = array('id','username','email','is_active','last_login','updated_at','credential_expire_at','roles');


        //$jsonContent = $serializer->serialize($serialized_user, 'json');

        return $this->render('user/index.html.twig', array(
            'users' => $users,
            'jsonContent' => json_encode($users)
            )
        );
        //return $this->render('user/index.html.twig', ['users' => null]);
    }

    /**
     * @Route("/user_ajax", name="admin_user_index_ajax", methods="GET|POST")
     */
    public function userAjax(Request $request) : Response
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer(), new DateTimeNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        // DB table to use
        $table = 'symfony.app_users';
        //$table = 'app_users';
        // Table's primary key
        $primaryKey = 'id';
         
        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        $columns = array(
            array( 'db' => 'id',        'dt' => 0 ),
            array( 'db' => 'username',  'dt' => 1 ),
            array( 'db' => 'email',     'dt' => 2 ),
            array( 'db' => 'is_active', 'dt' => 3,
                'formatter' => function( $d, $row ) {
                    return (int)$d == 1 ? '<span class="badge badge-success">YES</span>' : '<span class="badge badge-danger">NO</span>';
                }
            ),
            array(
                'db'        => 'last_login',
                'dt'        => 4,
                'formatter' => function( $d, $row ) {
                    return date( 'jS M y', strtotime($d));
                }
            ),
            array(
                'db'        => 'updated_at',
                'dt'        => 5,
                'formatter' => function( $d, $row ) {
                    return date( 'jS M y', strtotime($d));
                }
            ),
            array(
                'db'        => 'credential_expire_at',
                'dt'        => 6,
                'formatter' => function( $d, $row ) {
                    return date( 'jS M y', strtotime($d));
                }
            ),
            
            array(
                'db'        => 'roles',
                'dt'        => 7,
                'formatter' => function( $d, $row ) {
                    return preg_replace('/[\["\]]/', '',$d);
                }
            )
        );

        $sql_details = array(
            'user' => $this->db_user,
            'pass' => $this->db_password,
            'db'   => $this->db_name,
            'host' => $this->db_host
        );

        // ATTENTION SI var_dump($var) l'objet retourné n'est plus JSON
        //var_dump($_GET);
        $datatable_result = SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns );

        foreach ($datatable_result["data"] as $key => $row) {
            array_push( $row, $this->renderView('user/_index_actions_btn.html.twig', array("id" => $row[0]) )
            );
            $datatable_result["data"][$key] = $row;
        }

        //var_dump($datatable_result);

        return $response = new Response($serializer->serialize( $datatable_result, 'json'));

        /*echo json_encode(
            SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
        );*/
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
