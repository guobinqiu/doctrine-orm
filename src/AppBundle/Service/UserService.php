<?php

namespace AppBundle\Service;

use AppBundle\Entity\Group;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;

class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(EntityManager $em)
    {
        $this->userRepository = $em->getRepository('AppBundle\Entity\User');
    }

    public function createUser(array $attributes) {
        $user = new User();
        $user->setName($attributes['name']);
        $user->setEmail($attributes['email']);
        $user->setPassword($attributes['password']);
        $this->userRepository->createUser($user);
    }

    public function updateUser($id, array $attributes) {
        $user = $this->userRepository->find($id);
        $user->setName($attributes['name']);
        $user->setEmail($attributes['email']);
        $this->userRepository->updateUser($user);
    }

    public function deleteUser($id) {
        $user = $this->userRepository->find($id);
        $this->userRepository->deleteUser($user);
    }

    public function first() {
        return $this->userRepository->first();
    }

    public function findUserById($id) {
        return $this->userRepository->find($id);
    }

    public function findUsers() {
        return $this->userRepository->findUsers();
    }

    public function getUsersByGroup(Group $group) {
        return $this->userRepository->getUsersByGroup($group);
    }
}