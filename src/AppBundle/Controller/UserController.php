<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/users")
 */
class UserController extends Controller
{
    /**
     * @Route("/login", name="users_login", methods={"GET"})
     */
    public function loginAction()
    {
        return $this->render('user/login.html.twig');
    }

    /**
     * @Route("/logout", name="users_logout", methods={"DELETE"})
     */
    public function logoutAction(Request $request)
    {
        $request->getSession()->set('id', null);
        return $this->redirect($this->generateUrl('homepage'));
    }
}
