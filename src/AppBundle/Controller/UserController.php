<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
     * @Route("/", name="users")
     * @Method("GET")
     */
    public function indexAction()
    {
        $users = $this->get('app.user_service')->getAllUsers();
        return $this->render('user/index.html.twig', array('users' => $users));
    }

    /**
     * 新增一个user的页面
     *
     * @Route("/new", name="users_new")
     * @Method("GET")
     */
    public function newAction() {
        return $this->render('user/new.html.twig');
    }

    /**
     * 往表里新增一个用户
     *
     * @Route("/create", name="users_create")
     * @Method("POST")
     */
    public function createAction() {

        $request= $this->getRequest();

        //从post中取到提交参数
        $attributes = $request->request->get('user');

        $userService = $this->get("app.user_service");
        $userService->createUser($attributes);

        return $this->redirect($this->generateUrl('users'));
    }

    /**
     * 显示单个user的页面
     *
     * @Route("/{id}", name="users_show")
     * @Method("GET")
     */
//    public function showAction($id) {
//    }

    /**
     * 编辑单个user的页面
     *
     * @Route("/{id}/edit", name="users_edit")
     * @Method("GET")
     */
    public function editAction($id) {
        $user = $this->get('app.user_service')->findUserById($id);
        return $this->render('/user/edit.html.twig', array('user' => $user));
    }

    /**
     * 从表里修改一个存在的用户
     *
     * @Route("/{id}", name="users_update")
     * @Method("PUT")
     */
    public function updateAction($id) {

        $request= $this->getRequest();

        //从post中取到提交参数
        $attributes = $request->request->get('user');

        $userService = $this->get("app.user_service");
        $userService->updateUser($id, $attributes);

        return $this->redirect($this->generateUrl('users'));
    }

    /**
     * 从表里删除一个存在的用户
     *
     * @Route("/{id}", name="users_delete")
     * @Method("DELETE")
     */
    public function destroyAction($id) {
        $userService = $this->get("app.user_service");
        $userService->deleteUser($id);
        return $this->redirect($this->generateUrl('users'));
    }


    //////////////////////测试一对一双向关联

    /**
     * @Route("/getProfileByUser")
     */
    public function getProfileByUser() {
        $user = $this->get('app.user_service')->first();
        if (empty($user)) {
            $url = "<a href='/users'>Add</a>";
            return new Response("No user found. Go to {$url}");
        }
        $userProfile = $user->getUserProfile();
        return new Response('Gender=' . $userProfile->getGender() . ', Birthday=' . $userProfile->getBirthday()->format('Y-m-d'));
    }

    /**
     * @Route("/getUserByProfile")
     */
    public function getUserByProfile() {
        $userProfile = $this->get('app.user_profile_service')->first();
        if (empty($userProfile)) {
            $url = "<a href='/users'>Add</a>";
            return new Response("No user's profile found. Go to {$url}");
        }
        $user = $userProfile->getUser();
        return new Response($user);
    }
}
