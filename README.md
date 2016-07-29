# User Login Demo

## 下载安装ngrok
http://www.ngrok.cc/

## ngrok配置
登录http://www.ngrok.cc/login -> 隧道管理，配置如下：
- 隧道协议：http
- 隧道名称：随便填
- http域名类型：系统前缀
- 域名/远程端口：demo2016 
- 本地地址：127.0.0.1
- 本地端口：8000

## 启动ngrok
cd到ngrok安装目录执行：./sunny clientid fdce931cd9b6c65a
(其中｀fdce931cd9b6c65a｀是你在隧道管理页面配置后自动生成的客户端id）

## 启动symfony
php app/console server:run 127.0.0.1:8000

## Oauth Wechat
- https://open.weixin.qq.com/
- https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419316505&token=9a2b96f92b52443dc1591a76e5f484a59b28e77b&lang=zh_CN
- https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419316518&token=9a2b96f92b52443dc1591a76e5f484a59b28e77b&lang=zh_CN
- http://demo2016.ngrok.cc/wechat/login_callback

## Oauth QQ
- http://connect.qq.com/
- http://wiki.connect.qq.com/%E5%87%86%E5%A4%87%E5%B7%A5%E4%BD%9C_oauth2-0
- http://wiki.connect.qq.com/%E5%BC%80%E5%8F%91%E6%94%BB%E7%95%A5_server-side
- http://demo2016.ngrok.cc/qq/login_callback

## Oauth Weibo
- http://open.weibo.com/
- http://open.weibo.com/wiki/%E6%8E%88%E6%9D%83%E6%9C%BA%E5%88%B6
- http://open.weibo.com/wiki/%E5%BE%AE%E5%8D%9AAPI#.E7.94.A8.E6.88.B7
- http://demo2016.ngrok.cc/weibo/login_callback

## 安装Captcha
- https://github.com/Gregwar/CaptchaBundle
- php composer.phar require gregwar/captcha-bundle 1.1