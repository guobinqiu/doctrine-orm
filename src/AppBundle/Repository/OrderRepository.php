<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Order;
use Doctrine\ORM\EntityRepository;

/**
 * OrderRepository
 */
class OrderRepository extends EntityRepository
{
    public function createOrder(Order $order) {
        $em = $this->getEntityManager();
        $em->persist($order);
        $em->flush();
    }

    //相当于执行select o.* from orders o limit 1
    public function first() {
        return $this->createQueryBuilder('c')->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }
}
