<?php

namespace AppBundle\Service;

use AppBundle\Entity\Group;
use AppBundle\Entity\User;
use AppBundle\Entity\UserGroup;
use AppBundle\Repository\GroupRepository;
use Doctrine\ORM\EntityManager;

class GroupService
{
    /**
     * @var GroupRepository
     */
    private $groupRepository;

    public function __construct(EntityManager $em)
    {
        $this->groupRepository = $em->getRepository('AppBundle\Entity\Group');
    }

    public function createGroup(Group $group) {
        $this->groupRepository->createGroup($group);
    }

    public function addUserToGroup(Group $group, User $user) {
        $userGroup = new UserGroup();
        $userGroup->setGroup($group);

        //双向关联永远保证两头都设置
        $userGroup->setUser($user);
        $group->addUserGroup($userGroup);

        $this->groupRepository->createGroup($group);
    }

    public function addUsersToGroup(Group $group, array $users) {
        foreach($users as $user) {
            $this->addUserToGroup($group, $user);
        }
    }

    public function first() {
        return $this->groupRepository->first();
    }

    public function destroyAll() {
        $groups = $this->groupRepository->findAll();
        foreach($groups as $group) {
            $this->groupRepository->deleteGroup($group);
        }
    }
}