<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/reset_password")
 */
class ResetPasswordController extends Controller
{
    /**
     * @Route("/new", name="reset_password_new", methods={"GET"})
     */
    public function newAction()
    {
        return $this->render('reset_password/new.html.twig');
    }

    /**
     * @Route("/create", name="reset_password_create", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        $email = $request->request->get('email');

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->findOneBy(array('email' => $email));

        if ($user == null) {
            return $this->redirect($this->generateUrl('reset_password_new'));
        }

        $resetPasswordToken = md5($user->getId() . $user->getEmail() . $user->getPassword());
        $user->setResetPasswordToken($resetPasswordToken);
        $user->setResetPasswordSentAt(new \DateTime());

        $em->flush();

        //发邮件
        $emailBody = $this->renderView('reset_password/notification.html.twig', array(
            'email' => $email,
            'token' => $resetPasswordToken
        ));
        $message = \Swift_Message::newInstance()
            ->setSubject('91问问-帐号密码重置')
            ->setFrom('cs@91wenwen.net')
            ->setSender('7-3259@91wenwen-signup.webpower.asia')
            ->setTo($email)
            ->setBody($emailBody, 'text/html');
        $mailer = $this->container->get('mailer');
        $mailer->send($message);

        $request->getSession()->getFlashBag()->add('notice', 'Email has been sent');
        return $this->redirect($this->generateUrl('reset_password_new'));
    }

    /**
     * @Route("/check", name="reset_password_check", methods={"GET"})
     */
    public function checkAction(Request $request)
    {
        $resetPasswordToken = $request->query->get('token');

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')
            ->findOneBy(array('resetPasswordToken' => $resetPasswordToken));

        if ($user == null) {
            return $this->redirect($this->generateUrl('reset_password_new'));
        }

        if ($user->isResetPasswordTokenExpired()) {
            return $this->redirect($this->generateUrl('reset_password_new'));
        }

        $user->setResetPasswordSentAt(new \DateTime());
        $user->setResetPasswordToken(null);
        $em->flush();

        return $this->render('reset_password/update.html.twig', array('id' => $user->getId()));
    }

    /**
     * @Route("/{id}", name="reset_password_update", methods={"PUT"})
     */
    public function updateAction(Request $request, $id)
    {
        $plain_password = $request->request->get('password');

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->find($id);
        $encrypted_password = password_hash($plain_password, PASSWORD_BCRYPT);
        $user->setPassword($encrypted_password);

        $em->flush();

        return $this->render('reset_password/success.html.twig');
    }
}
