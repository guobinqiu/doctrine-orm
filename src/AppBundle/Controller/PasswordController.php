<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
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
     * @Route("/input_email", name="user_password_input_email", methods={"GET"})
     */
    public function inputEmailAction()
    {
        return $this->render('password/input_email.html.twig');
    }

    /**
     * Ajax调用
     *
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
        $user->setResetPasswordTokenExpiredAt(new \DateTime('+ 24 hour'));

        $em->flush();

        //发邮件
        $this->send_reset_password_email($user);

        return new JsonResponse(array('error' => false, 'message' => '邮件已发送'), 200);
    }

    /**
     * @Route("/edit", name="user_password_edit", methods={"GET"})
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

        return $this->render('password/edit.html.twig', array('reset_password_token' => $resetPasswordToken));
    }

    /**
     * @Route("/update", name="user_password_update", methods={"PUT"})
     */
    public function updateAction(Request $request)
    {
        $resetPasswordToken = $request->query->get('reset_password_token');

        //验证普通变量
        //http://stackoverflow.com/questions/18316166/symfony2-how-to-validate-an-email-address-in-a-controller
        $plain_password = $request->request->get('password');

        $validator = $this->container->get('validator');

        $notBlankConstraint = new \Symfony\Component\Validator\Constraints\NotBlank();
        $lengthConstraint = new \Symfony\Component\Validator\Constraints\Length(array('min' => 6));
        $errors = $validator->validateValue($plain_password, array($notBlankConstraint,$lengthConstraint));
        if (count($errors) > 0) {
            return $this->render('password/edit.html.twig', array(
                'errors' => $errors,
                'reset_password_token' => $resetPasswordToken
            ));
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')
            ->findOneBy(array('resetPasswordToken' => $resetPasswordToken));

        if ($user == null) {
            return $this->redirect($this->generateUrl('user_password_error', array('error' => '无效链接')));
        }

        $encrypted_password = password_hash($plain_password, PASSWORD_BCRYPT);
        $user->setPassword($encrypted_password);
        $user->setResetPasswordToken(null);
        $em->flush();

        return $this->redirect($this->generateUrl('user_password_success'));
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
