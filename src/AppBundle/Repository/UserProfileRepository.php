<?php

namespace AppBundle\Repository;

use AppBundle\Entity\UserProfile;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\User;

/**
 * UserProfileRepository
 */
class UserProfileRepository extends EntityRepository
{
    public function createUserProfile(UserProfile $userProfile) {
        $em = $this->getEntityManager();
        $em->persist($userProfile);
        $em->flush();
    }

    public function updateUserProfile(UserProfile $userProfile) {
        $em = $this->getEntityManager();
        $em->merge($userProfile);
        $em->flush();
    }

    public function deleteUserProfile(UserProfile $userProfile) {
        $em = $this->getEntityManager();
        $em->remove($userProfile);
        $em->flush();
    }

    public function first() {
        return $this->createQueryBuilder('p')->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }
}
