<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/user/password")
 */
class PasswordController extends Controller
{
    /**
     * @Route("/new", name="password_new", methods={"GET"})
     */
    public function inputEmailAction()
    {
        return $this->render('password/input_email.html.twig');
    }

    /**
     * @Route("/create", name="password_create", methods={"POST"})
     */
    public function sendResetPasswordEmailAction(Request $request)
    {
        $email = $request->request->get('email');

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->findOneBy(array('email' => $email));

        if ($user == null) {
            return $this->redirect($this->generateUrl('password_new'));
        }

        $resetPasswordToken = md5($user->getId() . $user->getEmail() . $user->getPassword());
        $user->setResetPasswordToken($resetPasswordToken);
        $user->setResetPasswordSentAt(new \DateTime());

        $em->flush();

        //发邮件
        $emailBody = $this->renderView('password/reset_password_email.html.twig', array(
            'email' => $email,
            'reset_password_token' => $resetPasswordToken
        ));
        $message = \Swift_Message::newInstance()
            ->setSubject('91问问-帐号密码重置')
            ->setFrom('cs@91wenwen.net')
            ->setSender('7-3259@91wenwen-signup.webpower.asia')
            ->setTo($email)
            ->setBody($emailBody, 'text/html');
        $mailer = $this->container->get('mailer');
        $mailer->send($message);

        return $this->redirect($this->generateUrl('password_new'));
    }

    /**
     * @Route("/edit", name="password_edit", methods={"GET"})
     */
    public function verifyResetPasswordTokenAction(Request $request)
    {
        $resetPasswordToken = $request->query->get('reset_password_token');

        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(array('resetPasswordToken' => $resetPasswordToken));

        if ($user == null) {
            return $this->redirect($this->generateUrl('password_new'));
        }

        if ($user->isResetPasswordTokenExpired()) {
            return $this->redirect($this->generateUrl('password_new'));
        }

        return $this->render('password/edit.html.twig', array('reset_password_token' => $resetPasswordToken));
    }

    /**
     * @Route("/update", name="password_update", methods={"PUT"})
     */
    public function updateAction(Request $request)
    {
        $resetPasswordToken = $request->query->get('reset_password_token');

        $plain_password = $request->request->get('password');

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')
            ->findOneBy(array('resetPasswordToken' => $resetPasswordToken));

        $encrypted_password = password_hash($plain_password, PASSWORD_BCRYPT);
        $user->setPassword($encrypted_password);
        $user->setResetPasswordToken(null);
        $em->flush();

        return $this->redirect($this->generateUrl('password_success'));
    }

    /**
     * @Route("/success", name="password_success", methods={"GET"})
     */
    public function showSuccessAction()
    {
        return $this->render('password/success.html.twig');
    }
}
