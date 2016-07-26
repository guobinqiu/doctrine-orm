<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/user/password")
 */
class PasswordController extends Controller
{
    /**
     * @Route("/request", name="user_password_request", methods={"GET"})
     */
    public function requestAction()
    {
        return $this->render('password/request.html.twig');
    }

    /**
     * @Route("/send_email", name="user_password_send_email", methods={"POST"})
     */
    public function sendEmailAction(Request $request)
    {
        $email = $request->request->get('email');

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->findOneBy(array('email' => $email));

        if ($user == null) {
            return new JsonResponse(array('error' => true, 'message' => '邮件不存在'), 404);
        }

        $resetPasswordToken = md5($user->getId() . $user->getEmail() . $user->getPassword());
        $user->setResetPasswordToken($resetPasswordToken);
        $user->setResetPasswordSentAt(new \DateTime());

        $em->flush();

        //发邮件
        $emailBody = $this->renderView('password/reset_password_email.html.twig', array('user' => $user));
        $message = \Swift_Message::newInstance()
            ->setSubject('91问问-帐号密码重置')
            ->setFrom('cs@91wenwen.net')
            ->setSender('7-3259@91wenwen-signup.webpower.asia')
            ->setTo($email)
            ->setBody($emailBody, 'text/html');
        $mailer = $this->container->get('mailer');
        $mailer->send($message);

        return new JsonResponse(array('error' => false, 'message' => '邮件已发送'), 200);
    }

    /**
     * @Route("/edit", name="user_password_edit", methods={"GET"})
     */
    public function editAction(Request $request)
    {
        $resetPasswordToken = $request->query->get('reset_password_token');

        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(array('resetPasswordToken' => $resetPasswordToken));

        if ($user == null) {
            return $this->redirect($this->generateUrl('user_password_request'));
        }

        if ($user->isResetPasswordTokenExpired()) {
            return $this->redirect($this->generateUrl('user_password_request'));
        }

        return $this->render('password/edit.html.twig', array('reset_password_token' => $resetPasswordToken));
    }

    /**
     * @Route("/update", name="user_password_update", methods={"PUT"})
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

        return $this->redirect($this->generateUrl('user_password_updated'));
    }

    /**
     * @Route("/updated", name="user_password_updated", methods={"GET"})
     */
    public function updatedAction()
    {
        return $this->render('password/updated.html.twig');
    }
}
