<?php

/*
* This file is part of the Symfony package.
*
* (c) Fabien Potencier <fabien@symfony.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

/*
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
* Controller used to manage the application security.
* See https://symfony.com/doc/current/cookbook/security/form_login_setup.html.
*
* @author Jerome Rabahi <j.rabahi@claravista.fr>
*/
/*
class AdminController extends AbstractController
{
    /**
     * @Route("/admintt/", name="app_admin_dashbord", methods="GET")
     */
    /*
    public function admin(AuthenticationUtils $helper, AuthorizationCheckerInterface $authChecker): Response
    {
        if (false === $authChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Unable to access this page!');
        }

        return $this->render('admin/admin.html.twig', [
            'cookie' => $_COOKIE,
            'test' => 'rest',
        ]);
    }
}
*/

// src/AppBundle/Controller/AdminController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/admin")
 */
class AdminController extends BaseAdminController
{
    /**
    * @var UserPasswordEncoderInterface
    */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function updateEntity($entity)
    {
        $this->encodeUserPassword($this->passwordEncoder, $entity);
        parent::updateEntity($entity);
    }
    public function persistEntity($entity)
    {
        $this->encodeUserPassword($this->passwordEncoder, $entity);
        parent::persistEntity($entity);
    }

    private function encodeUserPassword($passwordEncoder, $entity)
    {   
        if (method_exists($entity, 'setPassword') and method_exists($entity, 'getPlainPassword')) {
            $password = $passwordEncoder->encodePassword($entity, $entity->getPlainPassword());
            $entity->setPassword($password);
        }
    }
}