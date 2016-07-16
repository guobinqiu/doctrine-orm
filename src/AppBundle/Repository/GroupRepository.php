<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Group;
use Doctrine\ORM\EntityRepository;

/**
 * GroupRepository
 */
class GroupRepository extends EntityRepository
{
    public function createGroup(Group $group) {
        $em = $this->getEntityManager();
        $em->persist($group);
        $em->flush();
    }

    public function updateGroup(Group $group) {
        $em = $this->getEntityManager();
        if (!$em->contains($group)) {
            throw new InvalidArgumentException('传入的参数不是一个持久化过的对象');
        }
        $em->flush();
    }

    public function deleteGroup(Group $group) {
        $em = $this->getEntityManager();
        $em->remove($group);
        $em->flush();
    }

    //相当于执行select g.* from groups g limit 1
    public function first() {
        return $this->createQueryBuilder('g')->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }
}
