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
     * @Route("/login", name="user", methods={"GET"})
     */
    public function indexAction()
    {
        return $this->render('user/login.html.twig');
    }

    /**
     * @Route("/login", name="user_login", methods={"POST"})
     */
    public function loginAction(Request $request)
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('email' => $email));

        if ($user == null) {
            return $this->render('user/login.html.twig');
        }

        // http://php.net/manual/en/function.password-verify.php
        if (!password_verify($password, $user->getPassword())) {
            return $this->render('user/login.html.twig');
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
        return $this->redirect($this->generateUrl('homepage'));
    }
}
