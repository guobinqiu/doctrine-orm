<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/login", name="user_login", methods={"GET"})
     */
    public function loginAction()
    {
        return $this->render('user/login.html.twig');
    }

    /**
     * @Route("/login", name="user_check_login", methods={"POST"})
     */
    public function checkLoginCheckAction(Request $request)
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('email' => $email));

        if ($user == null) {
            return $this->redirect($this->generateUrl('user_login'));
        }

        // http://php.net/manual/en/function.password-verify.php
        if (!password_verify($password, $user->getPassword())) {
            return $this->redirect($this->generateUrl('user_login'));
        }

        $session = $request->getSession();
        $session->set('id', $user->getId());

        if ($session->get('back_url') != null) {
            $back_url = $session->get('back_url');
            $session->set('back_url', null);
            return $this->redirect($back_url);
        }

        return $this->redirect($this->generateUrl('homepage'));
    }

    /**
     * @Route("/logout", name="user_logout", methods={"DELETE"})
     */
    public function logoutAction(Request $request)
    {
        $request->getSession()->set('id', null);
        return $this->redirect($this->generateUrl('user_login'));
    }
}
