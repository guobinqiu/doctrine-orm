<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

//https://open.weixin.qq.com/
//https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419316505&token=9a2b96f92b52443dc1591a76e5f484a59b28e77b&lang=zh_CN
//https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419316518&token=9a2b96f92b52443dc1591a76e5f484a59b28e77b&lang=zh_CN
//http://demo2016.ngrok.cc/wechat/login_callback
class WechatLoginController extends Controller
{
    /**
     * http://open.weibo.com/wiki/Oauth2/authorize
     *
     * @Route("/wechat/login", name="wechat_login", methods={"GET"})
     */
    public function loginAction(Request $request)
    {
        $state = md5(uniqid(rand(), true));
        $request->getSession()->set('state', $state);

        $params = array(
            'appid' => $this->container->getParameter('wechat_app_id'),
            'redirect_uri' => $this->generateUrl('wechat_login_callback', array(), true),
            'response_type' => 'code',
            'scope' => 'snsapi_login',
            'state' => $state,
        );

        $url = 'https://open.weixin.qq.com/connect/qrconnect?' . http_build_query($params);
        return $this->redirect($url);
    }

    /**
     * @Route("/wechat/login_callback", name="wechat_login_callback", methods={"GET"})
     */
    public function loginCallbackAction(Request $request)
    {
        $code = $request->query->get('code');

        if ($code == null) {
            $this->get('logger')->error('[Wechat] User has cancelled grant.');
            return $this->redirect($this->generateUrl('user_login'));
        }

        $state = $request->query->get('state');
        if ($state != $request->getSession()->get('state')) {
            $this->get('logger')->error('[Wechat] The state does not match. You may be a victim of CSRF.');
            return $this->redirect($this->generateUrl('user_login'));
        }

        $msg = $this->getAccessToken($code);
        $token = $msg->access_token;
        $openId = $msg->openid;

        $userInfo = $this->getUserInfo($token, $openId);

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('openId' => $openId, 'provider' => 'Wechat'));

        if ($user == null) {
            $user = new User();
            $user->setName($userInfo->nickname);
            $user->setOpenId($openId);
            $user->setProvider('Wechat');
            $user->setConfirmed(User::CONFIRMED);
            $em->persist($user);
            $em->flush();
        }

        $session = $request->getSession();
        $session->set('id', $user->getId());

        if ($session->get('back_url') != null) {
            $back_url = $session->get('back_url');
            $session->remove('back_url');
            return $this->redirect($back_url);
        }

        return $this->redirect($this->generateUrl('homepage'));
    }

    private function getAccessToken($code)
    {
        $params = array(
            'appid' => $this->container->getParameter('wechat_app_id'),
            'secret' => $this->container->getParameter('wechat_app_secret'),
            'code' => $code,
            'grant_type' => 'authorization_code'
        );

        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query($params);
        $res = $this->get('guzzle.client')->get($url)->send();
        $resBody = $res->getBody();

//        正确的返回
//        {
//            "access_token":"ACCESS_TOKEN",
//            "expires_in":7200,
//            "refresh_token":"REFRESH_TOKEN",
//            "openid":"OPENID",
//            "scope":"SCOPE",
//            "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
//        }
//        错误的返回
//        {"errcode":40029,"errmsg":"invalid code"}
        $msg = json_decode($resBody);

        if (isset($msg->errcode)) {
            throw new \RuntimeException('[Wechat] ' . $msg->errmsg);
        }

        return $msg;
    }

    //https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419316518&token=9a2b96f92b52443dc1591a76e5f484a59b28e77b&lang=zh_CN
    private function getUserInfo($token, $openId) {
        $queryParams = array(
            'access_token' => $token,
            'openid' => $openId,
        );

        $url = 'https://api.weixin.qq.com/sns/userinfo?' . http_build_query($queryParams);
        $res = $this->get('guzzle.client')->get($url)->send();
        $resBody= $res->getBody();
        $msg = json_decode($resBody);

        if (isset($msg->errcode)) {
            throw new \RuntimeException('[Wechat] ' . $msg->errmsg);
        }

        return $msg;
    }
}
