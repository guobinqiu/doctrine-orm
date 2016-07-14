<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Customer;
use Doctrine\ORM\EntityRepository;

/**
 * CustomerRepository
 */
class CustomerRepository extends EntityRepository
{
    public function createCustomer(Customer $customer) {
        $em = $this->getEntityManager();
        $em->persist($customer); //保存临时对象(所谓临时对象是指还没有被em持久化进数据库过的用new创建出来的对象)
        $em->flush(); //->开启事务->执行insert->提交或回滚
    }

    public function updateCustomer(Customer $customer) {
        $em = $this->getEntityManager();
        $em->merge($customer);
        $em->flush(); //->开启事务->执行update->提交或回滚
    }

    public function deleteCustomer(Customer $customer) {
        $em = $this->getEntityManager();
        $em->remove($customer);
        $em->flush();
    }

    public function getAllCustomers() {
        return $this->findAll();
    }

    //DQL方式
    //参考 http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/dql-doctrine-query-language.html
    public function getAllCustomersWithDQL() {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM AppBundle\Entity\Customer c');
        return $query->getResult(); // array of Customer objects
    }

    //DQL QueryBuilder方式
    //参考 http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/query-builder.html
    public function getAllCustomersWithDQLQueryBuilder() {
//        $qb = $this->createQueryBuilder();
//        $qb->select('c')
//            ->from('AppBundle\Entity\Customer', 'c');
        $qb = $this->createQueryBuilder('c');

        //echo $qb->getDQL();

        $q = $qb->getQuery();
        return $q->getResult();
    }

    //原生SQL方式
    //参考 http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/data-retrieval-and-manipulation.html
    public function getAllCustomersWithSQL() {
        $conn = $this->getEntityManager()->getConnection();

//        $stmt = $conn->prepare('select * from customers');
//        $stmt->execute();

        //or
        $stmt = $conn->executeQuery('select * from customers');

        return $stmt->fetchAll();
    }

    //原生SQL QueryBuilder方式
    //参考 http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/query-builder.html
    public function getAllCustomersWithSQLQueryBuilder() {
        $conn = $this->getEntityManager()->getConnection();
        $qb = $conn->createQueryBuilder();
        $qb->select('*')
            ->from('customers');

        //echo $qb->getSQL();

        //$stmt = $qb->execute();//select返回\Doctrine\DBAL\Driver\Statement, insert,update,delete返回int

        //or
        $stmt = $conn->executeQuery($qb->getSQL());

        return $stmt->fetchAll();
    }

    public function first() {
        $q = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'asc')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery();
        return $q->getOneOrNullResult();
    }

    public function last() {
        $q = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'desc')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery();
        return $q->getOneOrNullResult();
    }

    public function deleteAll() {
        $em = $this->getEntityManager();
        $q = $em->createQuery('delete from AppBundle\Entity\Customer');
        return $q->execute();
    }

    public function destroyAll() {
        $customers = $this->findAll();
        foreach ($customers as $customer) {
            $this->deleteCustomer($customer);
        }
    }
}
