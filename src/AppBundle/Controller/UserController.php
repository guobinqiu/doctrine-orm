<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * (Restful CRUD)
 *
 * @Route("/users")
 *
 * app/console router:debug|grep users
 *
 * users                           GET    ANY    ANY  /users/
 * users_new                       GET    ANY    ANY  /users/new
 * users_create                    POST   ANY    ANY  /users/create
 * users_edit                      GET    ANY    ANY  /users/{id}/edit
 * users_update                    PUT    ANY    ANY  /users/{id}
 * users_delete                    DELETE ANY    ANY  /users/{id}
 */
class UserController extends Controller
{
    /**
     * 显示全部user的页面
     *
     * @Route("/", name="users", methods={"GET"})
     */
    public function indexAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        return $this->render('user/index.html.twig', array('users' => $users));
    }

    /**
     * 新增一个user的页面
     *
     * @Route("/new", name="users_new", methods={"GET"})
     */
    public function newAction() {
        return $this->render('user/new.html.twig');
    }

    /**
     * 往表里新增一个用户
     *
     * @Route("/create", name="users_create", methods={"POST"})
     */
    public function createAction(Request $request) {
        //从post中取到提交参数
        $attributes = $request->request->get('user');

        $user = new User();
        $user->setName($attributes['name']);
        $user->setEmail($attributes['email']);
        $user->setPassword($attributes['password']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $request->getSession()->getFlashBag()->add('success', '用户创建成功');
        return $this->redirect($this->generateUrl('users'));
    }

    /**
     * 显示单个user的页面
     *
     * @Route("/{id}", name="users_show", methods={"GET"})
     */
//    public function showAction($id) {
//    }

    /**
     * 编辑单个user的页面
     *
     * @Route("/{id}/edit", name="users_edit", methods={"GET"})
     */
    public function editAction($id) {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        return $this->render('user/edit.html.twig', array('user' => $user));
    }

    /**
     * 从表里修改一个存在的用户
     *
     * @Route("/{id}", name="users_update", methods={"PUT"})
     */
    public function updateAction(Request $request, $id) {
        //从post中取到提交参数
        $attributes = $request->request->get('user');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($id);
        $user->setName($attributes['name']);
        $user->setEmail($attributes['email']);
        $em->flush();

        $request->getSession()->getFlashBag()->add('success', '用户修改成功');
        return $this->redirect($this->generateUrl('users'));
    }

    /**
     * 从表里删除一个存在的用户
     *
     * @Route("/{id}", name="users_delete", methods={"DELETE"})
     */
    public function destroyAction($id) {
//        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
//        $em = $this->getDoctrine()->getManager();
//        $em->remove($user);
//        $em->flush();

        //换成下面这种写法更加漂亮，不会改变事务的边界
        //http://stackoverflow.com/questions/11846718/when-to-use-entity-manager-in-symfony2
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($id);
        $em->remove($user);
        $em->flush();

        return $this->redirect($this->generateUrl('users'));
    }
}
