# Doctrine ORM 测试Demo
比较Java的方式，是否可以实现的更加symfony一点？

# 本地访问的几个入口链接如下：
- http://localhost:8000/app_dev.php
- http://localhost:8000/app_dev.php/getCustomerByOrder
- http://localhost:8000/app_dev.php/getOrdersByCustomer
- http://localhost:8000/app_dev.php/getUsersByGroup
- http://localhost:8000/app_dev.php/users/
- http://localhost:8000/app_dev.php/users/getProfileByUser
- http://localhost:8000/app_dev.php/users/getUserByProfile

# 安装步骤
### 1. 安装数据库
php app/console doctrine:database:create

### 2. 建表
php app/console doctrine:schema:update --force
