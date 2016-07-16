<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Group;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository
{
    public function createUser(User $user) {
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }

    public function updateUser(User $user) {
        $em = $this->getEntityManager();
        if (!$em->contains($user)) {
            throw new InvalidArgumentException('传入的参数不是一个持久化过的对象');
        }
        $em->flush();
    }

    public function deleteUser(User $user) {
        $em = $this->getEntityManager();
        $em->remove($user);
        $em->flush();
    }

    //相当于执行select u.* from users u limit 1
    public function first() {
        return $this->createQueryBuilder('u')->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }

    public function findUsers() {
        return $this->findAll();
    }

    //http://doctrine-orm.readthedocs.io/projects/doctrine-orm/en/latest/reference/query-builder.html
    //相当于执行select users.* from users inner join users_groups on users_groups.user_id = users.id where users_groups.group_id = ?;
    public function getUsersByGroup(Group $group) {
        return $this->createQueryBuilder('u')
            ->join('AppBundle\Entity\UserGroup', 'ug')
            ->where('ug.group = :group')
            ->setParameter('group', $group)
            ->getQuery()
            ->getResult();
    }
}

