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
        $em->merge($group);
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
