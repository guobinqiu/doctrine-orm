<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use AppBundle\Entity\User;
use AppBundle\Entity\UserProfile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserProfileController
 *
 * @Route("/users/{user_id}/user_profiles")
 */
class UserProfileController extends Controller
{
    /**
     * @Route("/")
     * @Method("GET")
     */
    public function indexAction($user_id) {
        $user = $this->get('app.user_service')->findUserById($user_id);
        $userProfile = $user->getUserProfile();
        if (!$userProfile) {
            $userProfile = new UserProfile();
            $path = '/users/' . $user_id . '/user_profiles/';
            $_method = 'POST';
        } else {
            $path = '/users/' . $user_id . '/user_profiles/' . $userProfile->getId();
            $_method = 'PUT';
        }
        return $this->render('/user_profile/edit.html.twig', array(
            'user_profile' => $userProfile,
            'path' => $path,
            '_method' => $_method,
        ));
    }

    /**
     * @Route("/")
     * @Method("POST")
     */
    public function createAction($user_id) {

        $request= $this->getRequest();

        //从post中取到提交参数
        $attributes = $request->request->get('user_profile');

        $userProfileService = $this->get("app.user_profile_service");
        $userProfileService->createUserProfile($user_id, $attributes);

        return $this->redirect('/users/');
    }

    /**
     * @Route("/{id}")
     * @Method("PUT")
     */
    public function updateAction($id) {

        $request= $this->getRequest();

        //从post中取到提交参数
        $attributes = $request->request->get('user_profile');

        $userProfileService = $this->get("app.user_profile_service");
        $userProfileService->updateUserProfile($id, $attributes);

        return $this->redirect('/users/');
    }
}
