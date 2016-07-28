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
     * @Route("/register", name="user_registration_register", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->setAction($this->generateUrl('user_registration_register'))
            ->setMethod('POST')
            ->add('name', 'text')
            ->add('email', 'text')
            ->add('password', 'text')
            ->add('save', 'submit', array('label'=>'注册'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            //http://php.net/manual/en/function.password-hash.php
            $encrypted_password = password_hash($user->getPassword(), PASSWORD_BCRYPT);
            $user->setPassword($encrypted_password);

            //confirmation token要唯一，不用逆向
            //$token = bin2hex(openssl_random_pseudo_bytes(16));
            $confirmationToken = md5($user->getEmail() . $encrypted_password . time());
            $user->setConfirmationToken($confirmationToken);

            $user->setConfirmationTokenExpiredAt(new \DateTime('+ 24 hour'));
            $user->setConfirmed(User::UNCONFIRMED);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            //发激活码邮件
            $this->send_confirmation_email($user);

            $request->getSession()->set('email', $user->getEmail());
            //重定向防止页面被重复提交
            return $this->redirect($this->generateUrl('user_registration_reminder'));
        }

        return $this->render('registration/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/reminder", name="user_registration_reminder", methods={"GET"})
     */
    public function reminderAction(Request $request)
    {
        $email = $request->getSession()->get('email');
        return $this->render('registration/reminder.html.twig', array('email' => $email));
    }

    /**
     * @Route("/confirm", name="user_registration_confirm", methods={"GET"})
     */
    public function confirmAction(Request $request)
    {
        $confirmationToken = $request->query->get('confirmation_token');

        if ($confirmationToken == null) {
            return $this->redirect($this->generateUrl('user_registration_error', array('error' => '无效链接')));
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(array(
            'confirmed' => User::UNCONFIRMED,
            'confirmationToken' => $confirmationToken,
        ));

        if ($user == null) {
            return $this->redirect($this->generateUrl('user_registration_error', array('error' => '无效链接')));
        }

        if ($user->isConfirmationTokenExpired()) {
            return $this->redirect($this->generateUrl('user_registration_error', array('error' => '验证码已过期')));
        }

        $user->setConfirmed(User::CONFIRMED);
        $user->setConfirmationToken(null);

        $em->flush();

        $request->getSession()->set('id', $user->getId());

        return $this->redirect($this->generateUrl('user_registration_success'));
    }

    /**
     * @Route("/success", name="user_registration_success", methods={"GET"})
     */
    public function successAction(Request $request)
    {
        $id = $request->getSession()->get('id');
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        return $this->render('registration/success.html.twig', array('user' => $user));
    }

    /**
     * @Route("/error", name="user_registration_error", methods={"GET"})
     */
    public function errorAction(Request $request)
    {
        $error = $request->query->get('error');
        return $this->render('registration/error.html.twig', array('error' => $error));
    }

    private function send_confirmation_email(User $user)
    {
        $emailBody = $this->renderView('registration/confirmation_email.html.twig', array('user' => $user));

        $message = \Swift_Message::newInstance()
            ->setSubject('[91问问调查网] 请点击链接完成注册，开始有奖问卷调查')
            ->setFrom('cs@91wenwen.net')
            ->setSender('7-3259@91wenwen-signup.webpower.asia')
            ->setTo($user->getEmail())
            ->setBody($emailBody, 'text/html');
        $mailer = $this->container->get('mailer');
        return $mailer->send($message);
    }
}
