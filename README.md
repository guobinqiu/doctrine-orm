# Oauth Demo

## 下载安装ngrok
http://www.ngrok.cc/

## 配置ngrok
创建自定义guobin.ngrok.cfg

```
server_addr: "server.ngrok.cc:4443"
auth_token: "" #授权token，在www.ngrok.cc平台注册账号获取
tunnels:
  web:
   subdomain: "demo2016"
   proto:
    http: 127.0.0.1:8000 #映射端口，不加ip默认本机
```

## 启动ngrok
./ngrok -config guobin.ngrok.cfg start web

## 启动symfony
php app/console server:run 127.0.0.1:8000

# Oauth QQ
- http://connect.qq.com/
- http://wiki.connect.qq.com/%E5%87%86%E5%A4%87%E5%B7%A5%E4%BD%9C_oauth2-0