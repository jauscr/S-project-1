<?php
namespace Cittando\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

//use Cittando\SiteBundle\Controller\Base\RestClientController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Cittando\SiteBundle\Entity\UserVenueSaved;

class VenueController extends Controller
{
    /**
     * @Route("/venues", name="_venues")
     * @Template()
     */
    public function indexAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $e = 0;
        $filters = $this->getRequest()->query->all();
        $filters['limit'] = (isset($filters['limit'])) ? $filters['limit'] : 6;
        $filters['page'] = (isset($filters['page'])) ? $filters['page'] : 1;
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository("CittandoSiteBundle:Venue")->getPopular($filters);

        $data = array_merge($filters, $data);
        $data['slider'] = (count($data) > 0) ? array_slice($data, 0, 5) : array();

        if ($user != 'anon.') {
            $result['savedVenues'] = $em->getRepository('CittandoSiteBundle:Venue')->getVenuesByUser($user->getId());

            foreach ($result['savedVenues'] as $id) {
                $savedVenue[$e] = $id['venueVenue']['id'];
                $e++;
            }

            $data['isSaved'] = empty($savedVenue) ? array() : $savedVenue;
            $data = array_merge($filters, $data);
        } else {
            $data = array_merge($filters, $data);
        }

        $data['highlight'] = $em->getRepository("CittandoSiteBundle:City")->findBy(array('cityIsMetroarea'=> '1'),array('cityName' => 'ASC'),$limit=10);
        return $data;
    }

    /**
     * @Route("/venues/{id}", name="_venue_id")
     * @Template()
     */
    public function venueByIdAction($id)
    {
        $e =0;
        $em = $this->getDoctrine()->getManager();

        // get if the cinema is saved in this logged user
        $user = $this->get('security.context')->getToken()->getUser();
        if ($user != 'anon.')
        {
            $result = $em->getRepository('CittandoSiteBundle:UserVenueSaved')->getUserVenueSavedArray($user->getId(),$id);
            for($e=0; $e< count($result); $e++){
                $savedVenue[$e] =  $result[$e]['venueVenue']['id'];
            }
            $data['isSaved'] = empty($savedVenue) ? array() : $savedVenue;
        }
        else
        {
            $data['isSaved'] = array();
        }

        $data['venues'] = $em->getRepository("CittandoSiteBundle:Venue")->getVenue($id);
        //die(print_r($data));
        if ($this->container->get('request')->isXmlHttpRequest()) {
            $venues = json_encode($data['venues']);
            return new Response($venues);
        } else {
            return $this->render("CittandoSiteBundle:Venue:venue_detail.html.twig", $data);
        }
    }

    /**
     * Get user and event ID clicked
     * @Route("/venues/save/{VenueId}", name="_save_venue")
     * @return Response
     */
    public function saveVenueAction($VenueId)
    {
        $setData = new UserVenueSaved();
        $user = $this->get('security.context')->getToken()->getUser(); //Get current User Info

        if (is_numeric($user->getId())) {
            //find object information using parameter $EventId
            $Venue = $this->getDoctrine()
                ->getRepository('CittandoSiteBundle:Venue')
                ->find($VenueId);

            //find object information using parameter Current User $user
            $UserId = $this->getDoctrine()
                ->getRepository('CittandoSiteBundle:User')
                ->find($user->getId());

            //Save info
            $setData->setVenueVenue($Venue);
            $setData->setUserUser($UserId);
            $em = $this->getDoctrine()->getManager();
            $em->persist($setData);
            $em->flush();

            $data = true;
        } else {
            $data = false;
        }

        return new Response(json_encode($data));
    }

    /**
     * Delete user and venue ID clicked form UserVenueSaved
     * @Route("/venues/delete/{VenueId}", name="_delete_venue")
     * @return Response
     */

    public function deleteEventUserAction($VenueId)
    {
        $user = $this->get('security.context')->getToken()->getUser(); //Get current User Info
        if ($this->container->get('request')->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $result = $em->getRepository('CittandoSiteBundle:UserVenueSaved')->getUserVenueSaved($user->getId(), $VenueId);
            $em->remove($result);
            $em->flush();
            return new Response(json_encode(true));
        }
    }

    /**
     *
     * @Route("/venues/{idVenue}/time/{timeId}", name="_films_in_venue")
     */
    public function getFilmsInVenueAction($idVenue,$timeId){
        if ($this->container->get('request')->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $result = $em->getRepository('CittandoSiteBundle:FilmVenueRel')->getAllFilmsInVenue($idVenue,$timeId);

            return new Response(json_encode($result));
        }
    }
}
