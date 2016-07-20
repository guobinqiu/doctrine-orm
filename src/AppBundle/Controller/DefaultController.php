<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/", name="header")
     */
    public function headerAction(Request $request)
    {
        $current_user = null;

        $id = $request->getSession()->get('id');
        if ($id != null) {
            $current_user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        }

        return $this->render('default/header.html.twig', array('current_user' => $current_user));
    }
}
