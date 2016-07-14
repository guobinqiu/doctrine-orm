<?php

namespace AppBundle\Service;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Order;
use AppBundle\Repository\OrderRepository;
use Doctrine\ORM\EntityManager;

class OrderService
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    public function __construct(EntityManager $em)
    {
        $this->orderRepository = $em->getRepository('AppBundle\Entity\Order');
    }

    public function createOrder(Customer $customer) {
        $order = new Order();
        $order->setOrderNumber(random_int(100000, 999999));
        $order->setCustomer($customer);
        $this->orderRepository->createOrder($order);
    }

    public function first() {
        return $this->orderRepository->first();
    }
}