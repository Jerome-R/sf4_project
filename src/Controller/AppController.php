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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use App\Form\ClientType;
use App\Entity\Client;

use App\Service\MailBuilder;

/**
* Controller used to manage the application security.
* See https://symfony.com/doc/current/cookbook/security/form_login_setup.html.
*
* @author Jerome Rabahi <j.rabahi@claravista.fr>
*/
class AppController extends AbstractController
{
    /**
     * @var MailBuilder
     */
    private $mail_builder;

    public function __construct(MailBuilder $mail_builder)
    {
        $this->mail_builder = $mail_builder;
    }


    /**
     * @Route("/", name="app_homepage")
     */
    public function home(Request $request): Response
    {   
        $form = $this->createForm(ClientType::class);
        $form->handleRequest($request);

        return $this->render('app/homepage.html.twig', [
            'form'          => $form->createView(),
        ]);
    }
}
