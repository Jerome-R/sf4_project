<?php

/*
* This file is part of the Symfony package.
*
* (c) Fabien Potencier <fabien@symfony.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\MailBuilder;
use App\Service\RandomString;

use App\Form\UserType;
use App\Entity\User;

/**
* Controller used to manage the application security.
* See https://symfony.com/doc/current/cookbook/security/form_login_setup.html.
*
* @author Jerome Rabahi <j.rabahi@claravista.fr>
*/
class SecurityController extends AbstractController
{
/**
* @var ValidatorInterface
*/
private $validator;

/**
* @var MailBuilder
*/
private $mail_builder;

/**
* @var RandomString
*/
private $random_string;

public function __construct(ValidatorInterface $validator, MailBuilder $mail_builder, RandomString $random_string)
{
    $this->validator = $validator;
    $this->mail_builder = $mail_builder;
    $this->random_string = $random_string;
}

/**
* Login Page
*/
public function login(AuthenticationUtils $helper): Response
{
    return $this->render('security/login.html.twig', [
// last username entered by the user (if any)
        'last_username' => $helper->getLastUsername(),
// last authentication error (if any)
        'error' => $helper->getLastAuthenticationError(),
    ]);
}

/**
* Logout
* This is the route the user can use to logout.
*
* But, this will never be executed. Symfony will intercept this first
* and handle the logout automatically. See logout in app/config/security.yml
*
*/
public function logout(): void
{
    throw new \Exception('This should never be reached!');
}

    /**
    * Regiter page
    */
    public function register(UserPasswordEncoderInterface $passwordEncoder, Request $request): Response
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);

        $errors = $this->validator->validate($user);

        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            //$user->eraseCredentials();
            $user->setPassword($password);
            $user->setFullName($user->getUsername());
            //$user->setToken(bin2hex(random_bytes(101)));
            //$user->setToken(sha1(var_dump(microtime(true))));
            //$user->setToken(hash('sha256',bin2hex(random_bytes(101))));
            $user->setToken($this->random_string->getToken('alnum',100));
            $user->setIsActive(false);

            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Envoie email de notif
            $mailer = $this->mail_builder->getMailer();

            $options['objet'] = "[TEST] Ceci est un test d'authentification en 2 étapes";
            // Config a verified email on AWS SES here !!
            $options['from'] = ["clienteling@news-clarins.us" => "AdminAlias"];
            $options['replyTo'] = ['rabahi.jerome@gmail.com'];
            $options['to'] = [$user->getEmail()];
            $options['text'] = 'Claravista test';
            $options['html'] = $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                'email/default.html.twig',
                array(
                    'messageHtml'       => "Please valide your account at: <br />http://sf4-project/account_validation/".$user->getId()."/" . $user->getToken()
                )
            );

            $message = $this->mail_builder->createMessage($options);

            //Try to send message and get exeption if fail
            try{
                $mailer->send($message);
            //$var = 1;
            }
            catch(\Swift_TransportException $e){
                $success = 0;
                $response = $e->getMessage() ;
                $request->getSession()->getFlashBag()->add('notice', 'Error while sending the email.');
            //var_dump($response); die();
            }

            return $this->redirectToRoute('security_two_step_validation');
        }

        return $this->render(
            'security/register.html.twig',
            array(
                'errors' => $errors,
                'form' => $form->createView(),
            )
        );
    }

    public function two_step_validation(): Response
    {
        return $this->render(
            'security/two_step_validation.html.twig',
            array(
            )
        );
    }


    /**
    * @ParamConverter("user",  options={"mapping": {"id": "id"}})
    */
    public function account_validation(User $user, $token): Response
    {
        if($token != $user->getToken()){
            $text = "Invalid request !";
        }else{
            $user->setIsActive(true);
            $user->setToken(null);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $text = "Your account has been activated !";
        }

        return $this->render(
            'security/account_validation.html.twig',
            array(
                'text' => $text
            )
        );
    }
}
