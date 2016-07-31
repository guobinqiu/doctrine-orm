<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/login", name="user_login", methods={"GET", "POST"})
     */
    public function loginAction(Request $request)
    {
        //创建一个没有model对象的表单，createFormBuilder方法参数留空即可
        //http://symfony.com/doc/current/forms.html
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('user_login'))
            ->setMethod('POST')
            //放置一个email类型的输入框
            //参考http://symfony.com/doc/current/reference/forms/types/email.html
            ->add('email', 'email', array(//添加验证规则
                'constraints' => array(
                    new Assert\NotBlank(),//参考http://symfony.com/doc/current/reference/constraints/NotBlank.html
                    new Assert\Email(), //参考http://symfony.com/doc/current/reference/constraints/Email.html
                )
            ))
            //放置一个密码类型的输入框
            //参考http://symfony.com/doc/current/reference/forms/types/password.html
            ->add('password', 'password', array(
                'constraints' => array(
                    new Assert\NotBlank(),//参考http://symfony.com/doc/current/reference/constraints/NotBlank.html
                    new Assert\Length(array('min' => 6)),//参考http://symfony.com/doc/current/reference/constraints/Length.html
                )
            ))
            ->add('login', 'submit', array('label'=>'登录'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $email = $data['email'];
            $password = $data['password'];

            $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('email' => $email));

            if ($user == null) {
                $form->addError(new FormError('邮箱或密码错误'));
                return $this->render('user/login.html.twig', array('form' => $form->createView()));
            }

            // http://php.net/manual/en/function.password-verify.php
            if (!password_verify($password, $user->getPassword())) {
                $form->addError(new FormError('邮箱或密码错误'));
                return $this->render('user/login.html.twig', array('form' => $form->createView()));
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

        return $this->render('user/login.html.twig', array('form' => $form->createView()));
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
