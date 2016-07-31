<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Route("/user/password")
 */
class PasswordController extends Controller
{
    /**
     * @Route("/input_email", name="user_password_input_email", methods={"GET"})
     */
    public function inputEmailAction()
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('user_password_send_email'))
            ->setMethod('POST')
            ->add('email', 'email')
            ->add('captcha', 'captcha')
            ->add('send', 'submit')
            ->getForm();

        return $this->render('password/input_email.html.twig', array('form' => $form->createView()));
    }

    /**
     * Ajax调用
     *
     * @Route("/send_email", name="user_password_send_email", methods={"POST"})
     */
    public function sendEmailAction(Request $request)
    {
        $captcha = $request->request->get('form')['captcha'];
        if ($captcha != $request->getSession()->get('gcb_captcha')['phrase']) {
            return new JsonResponse(array('error' => true, 'message' => '验证码有误'), 404);
        }

        $em = $this->getDoctrine()->getManager();

        $email = $request->request->get('form')['email'];
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('email' => $email));

        if ($user == null) {
            return new JsonResponse(array('error' => true, 'message' => '邮件不存在'), 404);
        }

        $resetPasswordToken = md5($user->getId() . $user->getEmail() . $user->getPassword());
        $user->setResetPasswordToken($resetPasswordToken);
        $user->setResetPasswordTokenExpiredAt(new \DateTime('+ 24 hour'));

        $em->flush();

        //发邮件
        $this->send_reset_password_email($user);

        return new JsonResponse(array('error' => false, 'message' => '邮件已发送'), 200);
    }

    /**
     * @Route("/reset", name="user_password_reset", methods={"GET", "PUT"})
     */
    public function editAction(Request $request)
    {
        $resetPasswordToken = $request->query->get('reset_password_token');

        if ($resetPasswordToken == null) {
            return $this->redirect($this->generateUrl('user_password_error', array('error' => '无效链接')));
        }

        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(array('resetPasswordToken' => $resetPasswordToken));

        if ($user == null) {
            return $this->redirect($this->generateUrl('user_password_error', array('error' => '无效链接')));
        }

        if ($user->isResetPasswordTokenExpired()) {
            return $this->redirect($this->generateUrl('user_password_error', array('error' => '验证码已过期')));
        }

        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('user_password_reset', array('reset_password_token' => $resetPasswordToken)))
            ->setMethod('PUT')
            //参考http://symfony.com/doc/current/reference/forms/types/repeated.html
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 6)),
                )
            ))
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $resetPasswordToken = $request->query->get('reset_password_token');

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle:User')
                ->findOneBy(array('resetPasswordToken' => $resetPasswordToken));

            if ($user == null) {
                return $this->redirect($this->generateUrl('user_password_error', array('error' => '无效链接')));
            }

            $password = $form->getData()['password'];
            $encrypted_password = password_hash($password, PASSWORD_BCRYPT);
            $user->setPassword($encrypted_password);
            $user->setResetPasswordToken(null);
            $em->flush();

            return $this->redirect($this->generateUrl('user_password_success'));
        }

        return $this->render('password/reset.html.twig', array(
            'reset_password_token' => $resetPasswordToken,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/success", name="user_password_success", methods={"GET"})
     */
    public function successAction()
    {
        return $this->render('password/success.html.twig');
    }

    /**
     * @Route("/error", name="user_password_error", methods={"GET"})
     */
    public function errorAction(Request $request)
    {
        $error = $request->query->get('error');
        return $this->render('password/error.html.twig', array('error' => $error));
    }

    private function send_reset_password_email(User $user)
    {
        $emailBody = $this->renderView('password/reset_password_email.html.twig', array('user' => $user));
        $message = \Swift_Message::newInstance()
            ->setSubject('91问问-帐号密码重置')
            ->setFrom('cs@91wenwen.net')
            ->setSender('7-3259@91wenwen-signup.webpower.asia')
            ->setTo($user->getEmail())
            ->setBody($emailBody, 'text/html');
        $mailer = $this->container->get('mailer');
        return $mailer->send($message);
    }
}
