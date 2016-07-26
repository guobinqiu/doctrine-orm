<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

//http://open.weibo.com/
//http://open.weibo.com/wiki/%E6%8E%88%E6%9D%83%E6%9C%BA%E5%88%B6
//http://open.weibo.com/wiki/%E5%BE%AE%E5%8D%9AAPI#.E7.94.A8.E6.88.B7
//http://demo2016.ngrok.cc/weibo/login_callback
class WeiboLoginController extends Controller
{
    const CLIENT_ID = '3163097902';
    const CLIENT_SECRET = 'af3e112c53a2357b201f3ddd6c8e69e7';

    /**
     * http://open.weibo.com/wiki/Oauth2/authorize
     *
     * @Route("/weibo/login", name="weibo_login", methods={"GET"})
     */
    public function grantAction(Request $request)
    {
        $state = md5(uniqid(rand(), true));
        $request->getSession()->set('state', $state);

        $params = array(
            'client_id' => self::CLIENT_ID,
            'redirect_uri' => $this->generateUrl('weibo_login_callback', array(), true),
            'state' => $state,
        );

        $url = 'https://api.weibo.com/oauth2/authorize?' . http_build_query($params);
        return $this->redirect($url);
    }

    /**
     * @Route("/weibo/login_callback", name="weibo_login_callback", methods={"GET"})
     */
    public function loginCallbackAction(Request $request)
    {
        $code = $request->query->get('code');

        if ($code == null) {
            $this->container->get('logger')->error('Grant was cancelled');
            return $this->redirect($this->generateUrl('user_login'));
        }

        $state = $request->query->get('state');
        if ($state != $request->getSession()->get('state')) {
            $this->container->get('logger')->error('The state does not match. You may be a victim of CSRF.');
            return $this->redirect($this->generateUrl('user_login'));
        }

        $token = $this->getAccessToken($code);

        $openId = $this->getOpenId($token);

        $userInfo = $this->getUserInfo($token, $openId);

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('openId' => $openId, 'provider' => 'weibo'));

        if ($user == null) {
            $user = new User();
            $user->setName($userInfo->screen_name);
            $user->setOpenId($openId);
            $user->setProvider('weibo');
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

    //http://open.weibo.com/wiki/Oauth2/access_token
    private function getAccessToken($code)
    {
        $url = 'https://api.weibo.com/oauth2/access_token';

        $request = $this->get('guzzle.client')->post($url);
        $request->addPostFields(array(
            'grant_type' => 'authorization_code',
            'client_id' => self::CLIENT_ID,
            'client_secret' => self::CLIENT_SECRET,
            'code' => $code,
            'redirect_uri' => $this->generateUrl('weibo_login_callback', array(), true),
        ));
        $res = $request->send();

//        返回数据
//        {
//            "access_token": "ACCESS_TOKEN",
//            "expires_in": 1234,
//            "remind_in":"798114",
//            "uid":"12341234"
//        }
        $resBody = $res->getBody();

        return json_decode($resBody)->access_token;
    }

    //http://open.weibo.com/wiki/Oauth2/get_token_info
    private function getOpenId($token) {
        $url = 'https://api.weibo.com/oauth2/get_token_info';
        $request = $this->get('guzzle.client')->post($url);
        $request->setPostField('access_token', $token);
        $res = $request->send();

//        返回数据
//        {
//            "uid": 1073880650,
//            "appkey": 1352222456,
//            "scope": null,
//            "create_at": 1352267591,
//            "expire_in": 157679471
//        }
        $resBody = $res->getBody();

        return json_decode($resBody)->uid;
    }

    //http://open.weibo.com/wiki/2/users/show
    private function getUserInfo($token, $openId) {
        $queryParams = array(
            'access_token' => $token,
            'uid' => $openId,
        );

        $url = 'https://api.weibo.com/2/users/show.json?' . http_build_query($queryParams);
        $res = $this->get('guzzle.client')->get($url)->send();

        $resBody= $res->getBody();

        return json_decode($resBody);
    }
}
