<?php

namespace Cittando\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MapController extends Controller
{
    /**
     * @Route("/map", name="_map")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
