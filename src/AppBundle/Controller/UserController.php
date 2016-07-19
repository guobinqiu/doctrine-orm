<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/users")
 */
class UserController extends Controller
{
    /**
     * @Route("/login", name="users_login", methods={"GET"})
     */
    public function LoginAction()
    {
        return $this->render('user/login.html.twig');
    }
}
