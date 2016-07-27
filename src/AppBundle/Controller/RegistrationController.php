<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/user/registration")
 */
class RegistrationController extends Controller
{
    /**
     * @Route("/new", name="user_registration_new", methods={"GET"})
     */
    public function newAction()
    {
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->setAction($this->generateUrl('user_registration_create'))
            ->setMethod('POST')
            ->add('name', 'text')
            ->add('email', 'text')
            ->add('password', 'text')
            ->add('save', 'submit', array('label'=>'注册'))
            ->getForm();

        return $this->render('registration/new.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/create", name="user_registration_create", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->setAction($this->generateUrl('user_registration_create'))
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
            $confirmationToken = md5($user->getEmail() . $user->getPassword() . time());
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
                ->setTo($user->getEmail())
                ->setBody($emailBody, 'text/html');
            $mailer = $this->container->get('mailer');
            $mailer->send($message);

            //重定向到另外一个页面防止重复提交
            return $this->redirect($this->generateUrl('user_registration_check_email', array('email' => $user->getEmail())));
        }

        return $this->render('registration/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/check_email", name="user_registration_check_email", methods={"GET"})
     */
    public function checkEmailAction(Request $request)
    {
        $email = $request->query->get('email');
        return $this->render('registration/check_email.html.twig', array('email' => $email));
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
