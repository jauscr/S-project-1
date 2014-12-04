<?php

namespace Cittando\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

Class UserController extends Controller{
    /**
     * @Route("/users", name="_user")
     * @Template()
     */
    public function indexAction()
    {
        $user = $this->get('security.context')->getToken()->getUser(); //Get current User Info

        if ($user == 'anon.') {
           return $this->redirect('login');
        }

        $em = $this->getDoctrine()->getManager();
        $result['events'] = $em->getRepository('CittandoSiteBundle:Event')->getEventsByUser($user->getId());
        $result['venues'] = $em->getRepository('CittandoSiteBundle:Venue')->getVenuesByUser($user->getId());
        $result['films'] = $em->getRepository('CittandoSiteBundle:UserFilmSaved')->getFilmsByUser($user->getId());
        $result['size'] = array(0 => array("numEvents" => sizeof($result['events']), "numVenues" => sizeof($result['venues']), "numFilms" => sizeof($result['films'])));

        //die(print_r($result));
        return $this->render("CittandoSiteBundle:User:index.html.twig", $result);
    }

    /**
     * GET /users/{userId}/events
     *
     * @param int $userId
     * @return Response
     */
    public function getUserEventsAction($userId)
    {
        $user = $this->getEntityManager()->getRepository('CittandoApiBundle:Event')->getEventsByUser($userId);
        $this->setViewData($user)->setStatusCode(200);
        return $this->getViewHandler();
    }
}
