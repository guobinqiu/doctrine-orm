<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserProfile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserProfileController
 *
 * @Route("/users/{user_id}/user_profiles")
 *
 * app/console router:debug|grep user_profiles
 * user_profiles                   GET    ANY    ANY  /users/{user_id}/user_profiles/
 * user_profiles_create            POST   ANY    ANY  /users/{user_id}/user_profiles/
 * user_profiles_update            PUT    ANY    ANY  /users/{user_id}/user_profiles/{id}
 */
class UserProfileController extends Controller
{
    /**
     * @Route("/", name="user_profiles", methods={"GET"})
     */
    public function indexAction($user_id) {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($user_id);
        $userProfile = $user->getUserProfile();
        if (!$userProfile) {
            $userProfile = new UserProfile();
            $path = $this->generateUrl('user_profiles_create', array('user_id' => $user_id));
            $_method = 'POST';
        } else {
            $path = $this->generateUrl('user_profiles_update', array(
                'user_id' => $user_id,
                'id' => $userProfile->getId(),
            ));
            $_method = 'PUT';
        }
        return $this->render('user_profile/edit.html.twig', array(
            'user_profile' => $userProfile,
            'path' => $path,
            '_method' => $_method,
        ));
    }

    /**
     * @Route("/", name="user_profiles_create", methods={"POST"})
     */
    public function createAction(Request $request, $user_id) {
        //从post中取到提交参数
        $attributes = $request->request->get('user_profile');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($user_id);

        $userProfile = new UserProfile();
        $userProfile->setGender($attributes['gender']);
        $userProfile->setBirthday(new \DateTime($attributes['birthday']));
        $userProfile->setUser($user);

        $em->persist($userProfile);
        $em->flush();

        $request->getSession()->getFlashBag()->add('success', '简介创建成功');
        return $this->redirect($this->generateUrl('users'));
    }

    /**
     * @Route("/{id}", name="user_profiles_update", methods={"PUT"})
     */
    public function updateAction(Request $request, $id) {
        //从post中取到提交参数
        $attributes = $request->request->get('user_profile');

        $em = $this->getDoctrine()->getManager();

        $userProfile = $em->getRepository('AppBundle:UserProfile')->find($id);
        $userProfile->setGender($attributes['gender']);
        $userProfile->setBirthday(new \DateTime($attributes['birthday']));

        $em->flush();

        $request->getSession()->getFlashBag()->add('success', '简介修改成功');
        return $this->redirect($this->generateUrl('users'));
    }
}
