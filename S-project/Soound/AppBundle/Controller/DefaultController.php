<?php

namespace Soound\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DefaultController extends Controller
{
	public function redirectToAction(){
        if ($this->container->get('request')->isXmlHttpRequest()){
            $url = $userName = $this->get('request')->request->get('url');
            $session = $this->getRequest()->getSession();
            $session->set('redirectTo', $url);
            return new Response(1);
        }
    }
}