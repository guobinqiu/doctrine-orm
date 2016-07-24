<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/user/registration")
 */
class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="user_registration_register", methods={"GET"})
     */
    public function registerAction()
    {
        return $this->render('registration/register.html.twig');
    }

    /**
     * @Route("/check_email", name="user_registration_check_email", methods={"POST"})
     */
    public function checkEmailAction(Request $request)
    {
        $attributes = $request->request->get('user');

        $name = $attributes['name'];
        $email = $attributes['email'];
        $plain_password = $attributes['password'];

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);

        //http://php.net/manual/en/function.password-hash.php
        $encrypted_password = password_hash($plain_password, PASSWORD_BCRYPT);
        $user->setPassword($encrypted_password);

        //confirmation token要唯一，不用逆向
        //$token = bin2hex(openssl_random_pseudo_bytes(16));
        $confirmationToken = md5($email . $encrypted_password . time());
        $user->setConfirmationToken($confirmationToken);

        //confirmation token的创建时间，不是邮件发送的时间
        $user->setConfirmationSentAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        //发邮件
        $emailBody = $this->renderView('registration/confirmation_email.html.twig', array('user' => $user));

        $message = \Swift_Message::newInstance()
            ->setSubject('[91问问调查网] 请点击链接完成注册，开始有奖问卷调查')
            ->setFrom('cs@91wenwen.net')
            ->setSender('7-3259@91wenwen-signup.webpower.asia')
            ->setTo($email)
            ->setBody($emailBody, 'text/html');
        $mailer = $this->container->get('mailer');
        $mailer->send($message);

        return $this->render('registration/checkEmail.html.twig', array('user' => $user));
    }

    /**
     * @Route("/confirm", name="user_registration_confirm", methods={"GET"})
     */
    public function confirmAction(Request $request)
    {
        $confirmationToken = $request->query->get('confirmation_token');

        if ($confirmationToken == null) {
            return $this->redirect($this->generateUrl('user_login'));
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('confirmationToken' => $confirmationToken));

        if ($user == null) {
            return $this->redirect($this->generateUrl('user_login'));
        }

        if ($user->isConfirmationTokenExpired()) {
            return $this->redirect($this->generateUrl('user_login'));
        }

        $user->setConfirmedAt(new \DateTime());
        $user->setConfirmationToken(null);
        $em->flush();

        $request->getSession()->set('id', $user->getId());

        return $this->redirect($this->generateUrl('user_registration_confirmed'));
    }

    /**
     * @Route("/confirmed", name="user_registration_confirmed", methods={"GET"})
     */
    public function comfirmedAction(Request $request)
    {
        $id = $request->getSession()->get('id');
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        return $this->render('registration/confirmed.html.twig', array('user' => $user));
    }
}
