<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Group;
use AppBundle\Entity\Order;
use AppBundle\Entity\User;
use AppBundle\Entity\UserGroup;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * 单表CRUD
     *
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
//        $customer = new Customer();
//        $customer->setName('Jack');
//        $customer->setAge('30');

//        $em = $this->getDoctrine()->getManager();
//        $em->persist($customer);
//        $em->flush();

        //重构到自定义Repository（类似Dao）
//        $customerDao = $this->getDoctrine()->getRepository('AppBundle:Customer');
//        $customerDao->deleteAll();
//
//        //新增
//        for ($i=0; $i<10; $i++) {
//            $customer = new Customer();
//            $customer->setName('Jack');
//            $customer->setAge('30');
//            $customerDao->createCustomer($customer);
//        }
//        //修改
//        $customer = $customerDao->first(2);
//        $customer->setAge('29');
//        $customerDao->updateCustomer($customer);
//
//        //删除
//        $customer = $customerDao->last();
//        $customerDao->deleteCustomer($customer->getId());
//
////        $customers = $customerDao->getAllCustomers();
////        $customers = $customerDao->getAllCustomersWithDQL();
////        $customers = $customerDao->getAllCustomersWithDQLQueryBuilder();
////        $customers = $customerDao->getAllCustomersWithSQL();
//        $customers = $customerDao->getAllCustomersWithSQLQueryBuilder();

        //重构到service里
        $customerService = $this->get('app.customer_service');
        $customerService->destroyAll();

        //新增
        for ($i=0; $i<10; $i++) {
            $customer = new Customer();
            $customer->setName('Guobin' . $i);
            $customer->setAge(30 + $i);
            $customerService->createCustomer($customer);
        }

        //修改
        $customer = $customerService->first();
        $customer->setAge('29');
        $customerService->updateCustomer($customer);

        //删除
        $customer = $customerService->last();
        $customerService->deleteCustomer($customer->getId());

//        $customers = $customerService->getAllCustomers();
//        $customers = $customerService->getAllCustomersWithDQL();
//        $customers = $customerService->getAllCustomersWithQueryBuilder();
//        $customers = $customerService->getAllCustomersWithSQL();
        $customers = $customerService->getAllCustomersWithSQLQueryBuilder();

        //下单
        $customer = $customerService->first();
        $orderService = $this->get('app.order_service');
        for ($i=0; $i<10; $i++) {
            $orderService->createOrder($customer);
        }

        return $this->render('default/index.html.twig', array('customers' => $customers));
    }

    /**
     * 根据Order查Customer
     *
     * @Route("/getCustomerByOrder")
     */
    public function getCustomerByOrder() {
        $orderService = $this->get('app.order_service');
        $order = $orderService->first();
        return $this->render('default/getCustomerByOrder.html.twig', array('order' => $order));
    }

    /**
     * 根据Customer查Orders
     *
     * @Route("/getOrdersByCustomer")
     */
    public function getOrdersByCustomer() {
        $customerService = $this->get('app.customer_service');
        $customer = $customerService->first();
        return $this->render('default/getOrdersByCustomer.html.twig', array('customer' => $customer));
    }


    /**
     * @Route("/getUsersByGroup")
     */
    public function getUsersByGroup() {

        $groupService = $this->get('app.group_service');
        $userService = $this->get('app.user_service');

        $groupService->destroyAll();

        ///////////////把所有用户关联到Admin组
        $group = new Group();
        $group->setName('Admin');
        $groupService->createGroup($group);

        $group = $groupService->first();
        $users = $userService->getAllUsers();
        $groupService->addUsersToGroup($group, $users);
        ///////////////

        $users = $userService->getUsersByGroup($group);
        if (empty($users)) {
            $url = "<a href='/users'>Add</a>";
            return new Response("No users found. Go to {$url}");
        }

        return $this->render('default/getUsersByGroup.html.twig', array(
            'group' => $group,
            'users' => $users
        ));
    }
}
