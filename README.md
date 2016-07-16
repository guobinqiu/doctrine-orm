# Doctrine ORM 测试Demo
该版本没有service层

# 本地访问的入口链接如下：
- http://localhost:8000/app_dev.php/users/

# 安装步骤
### 1. 安装数据库
php app/console doctrine:database:create

### 2. 建表
php app/console doctrine:schema:update --force
