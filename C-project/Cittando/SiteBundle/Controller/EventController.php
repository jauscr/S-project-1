<?php

namespace Cittando\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Cittando\SiteBundle\Form\Type\EventForm;
use Cittando\SiteBundle\Entity\UserEventSaved;

class EventController extends Controller
{
    /**
     * @Route("/events", name="_events")
     * @Template()
     */
    public function indexAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $e= 0;
        $filters = $this->getRequest()->query->all();
        $filters['limit'] = (isset($filters['limit'])) ? $filters['limit'] :6;
        $filters['page'] = (isset($filters['page'])) ? $filters['page'] : 1 ;
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository("CittandoSiteBundle:Event")->getPopular($filters);
        $data = empty($data) ? array() : $data;
        $data['slider'] = (count($data) > 0)?array_slice($data,0,5):array();

        if ($user != 'anon.') {
            $result['savedEvents'] = $em->getRepository("CittandoSiteBundle:Event")->getEventsByUser($user->getId());

            foreach ($result['savedEvents'] as $id) {
                $savedEvent[$e] = $id['eventEvent']['id'];
                $e++;
            }
            $data['isSaved'] = empty($savedEvent) ? array() : $savedEvent;
            $data = array_merge($filters, $data);
        }
        else{
            $data = array_merge($filters, $data);
        }

        if ($this->container->get('request')->isXmlHttpRequest())
        {
            $toMap = $em->getRepository("CittandoSiteBundle:Event")->getPopularToMap($filters);
            return new Response(json_encode($toMap));
        }
        else{
            $data['highlight'] = $em->getRepository("CittandoSiteBundle:City")->findBy(array('cityIsMetroarea'=> '1'),array('cityName' => 'ASC'),$limit=10);
            return $data;
        }

    }

    /**
     * @Route("/events/new", name="_event_new")
     * @Template()
     */
    public function newAction(Request $request)
    {

        $formData['categories'] = $this->getResource('event/categories')->getResponse();
        $formData['categories'] = $this->arrayToList($formData['categories'], 'category_id', 'category_name');

        $formData['venues'] = $this->getResource('venues')->getResponse();
        $formData['venues'] = $this->arrayToList($formData['venues'], 'venue_id', 'venue_name');

        $form = $this->createForm(new EventForm(), $formData);
        $viewData['form'] = $form->createView();
        $viewData['uri'] = $this->getResource("events")->getUri();

        return $viewData;
    }

    /**
     * @Route("/events/{id}", name="_event_id")
     * @Template()
     */
    public function eventByIdAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $data['events'] = $em->getRepository("CittandoSiteBundle:Event")->getEvent($id);
        //die(print_r($data));
        if ($this->container->get('request')->isXmlHttpRequest())
        {
            $event = json_encode($data['events']);
            return new Response($event);
        }
        else
        {
            return $this->render("CittandoSiteBundle:Event:event_detail.html.twig", $data);
        }

    }

    /**
     * Get user and event ID clicked
     * @Route("/events/save/{EventId}", name="_save_event")
     * @return Response
     */
public function saveEventAction($EventId){
    $setData = new UserEventSaved();
    $user= $this->get('security.context')->getToken()->getUser(); //Get current User Info

    if(is_numeric($user->getId())){
        //find object information using parameter $EventId
        $Event = $this->getDoctrine()
            ->getRepository('CittandoSiteBundle:Event')
            ->find($EventId);

        //find object information using parameter Current User $user
        $UserId = $this->getDoctrine()
            ->getRepository('CittandoSiteBundle:User')
            ->find($user->getId());

        //Save info
        $setData->setEventEvent($Event);
        $setData->setUserUser($UserId);
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($setData);
        $em->flush();
        $data = true;
    }
    else
    {
      $data = false;
    }

    return new Response(json_encode($data));
}

    /**
     * Get user and event ID tu remove event saved
     * @Route("/events/delete/{EventId}", name="_remove_event")
     * @return Response
     */
    public function removeEventUserAction($EventId){
        $user = $this->get('security.context')->getToken()->getUser(); //Get current User Info
        if ($this->container->get('request')->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $result = $em->getRepository('CittandoSiteBundle:UserEventSaved')->getUserEventSaved($user->getId(), $EventId);
            $em->remove($result);
            $em->flush();
            return new Response(json_encode(true));
        }
    }
}