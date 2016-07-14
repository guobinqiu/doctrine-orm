<?php

namespace AppBundle\Service;

use AppBundle\Entity\Customer;
use AppBundle\Repository\CustomerRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

class CustomerService
{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

//注入具体Repository的方式配置太麻烦了需要简化
//    public function __construct(CustomerRepository $customerRepository)
//    {
//        $this->customerRepository = $customerRepository;
//    }

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->customerRepository = $em->getRepository('AppBundle\Entity\Customer');
        $this->em = $em;
    }

    public function createCustomer(Customer $customer) {
        $this->customerRepository->createCustomer($customer);

//        //测试嵌套事务
//        //结论：外层事务出错了内层事务也会一起回滚，所以在dao里可以加事务
//        $this->em->getConnection()->beginTransaction();//外层事务
//        try {
//            $this->customerRepository->createCustomer($customer);//内层隐式事务
//            throw new RuntimeException('假设这里出错了');//程序执行到这里出错了上面的事务并没有提交
//            $this->em->commit();
//        } catch (\Exception $e) {
//            $this->em->getConnection()->rollBack();
//            throw $e;
//        }
    }

    public function updateCustomer(Customer $customer) {
        $this->customerRepository->updateCustomer($customer);
    }

    public function deleteCustomer($id) {
        $customer = $this->customerRepository->find($id);
        $this->customerRepository->deleteCustomer($customer);
    }

    public function getAllCustomers() {
        return $this->customerRepository->getAllCustomers();
    }

    public function getAllCustomersWithDQL() {
        return $this->customerRepository->getAllCustomersWithDQL();
    }

    public function getAllCustomersWithDQLQueryBuilder() {
        return $this->customerRepository->getAllCustomersWithDQLQueryBuilder();
    }

    public function getAllCustomersWithSQL(){
        return $this->customerRepository->getAllCustomersWithSQL();
    }

    public function getAllCustomersWithSQLQueryBuilder() {
        return $this->customerRepository->getAllCustomersWithSQLQueryBuilder();
    }

    public function first() {
        return $this->customerRepository->first();
    }

    public function last() {
        return $this->customerRepository->last();
    }

    public function deleteAll() {
        return $this->customerRepository->deleteAll();
    }

    //关联删除
    public function destroyAll() {
        return $this->customerRepository->destroyAll();
    }
}