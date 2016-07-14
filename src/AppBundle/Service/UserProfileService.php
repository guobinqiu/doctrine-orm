<?php

namespace AppBundle\Service;

use AppBundle\Entity\UserProfile;
use AppBundle\Repository\UserProfileRepository;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;

class UserProfileService
{
    /**
     * @var UserProfileRepository
     */
    private $userProfileRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(EntityManager $em)
    {
        $this->userProfileRepository = $em->getRepository('AppBundle\Entity\UserProfile');
        $this->userRepository = $em->getRepository('AppBundle\Entity\User');
    }

    public function createUserProfile($user_id, $attributes) {
        $user = $this->userRepository->find($user_id);
        $userProfile = new UserProfile();
        $userProfile->setGender($attributes['gender']);
        $userProfile->setBirthday(new \DateTime($attributes['birthday']));
        $userProfile->setUser($user);
        $this->userProfileRepository->createUserProfile($userProfile);
    }

    public function updateUserProfile($id, $attributes) {
        $userProfile = $this->userProfileRepository->find($id);
        $userProfile->setGender($attributes['gender']);
        $userProfile->setBirthday(new \DateTime($attributes['birthday']));
        $this->userProfileRepository->updateUserProfile($userProfile);
    }

    public function first() {
        return $this->userRepository->first();
    }
}