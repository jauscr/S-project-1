<?php

namespace Cittando\SiteBundle\Controller;

use Cittando\SiteBundle\Entity\City;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncode;


use Doctrine\Common\Util\Debug;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="_cittando_home")
     * @Template()
     */
    public function indexAction()
    {
        $filters = $this->getRequest()->query->all();
        $em = $this->getDoctrine()->getManager();
        $events = $em->getRepository("CittandoSiteBundle:Event")->getPopular($filters);
        $venues = $em->getRepository("CittandoSiteBundle:Venue")->getPopular($filters);
        $films = $em->getRepository("CittandoSiteBundle:Film")->getPopular($filters);
        /*--------------------------------------------------------------------------*/
        $session = $this->getRequest()->getSession();
        $tmpSession = $session->get('cittandoCityRequest');
        if(!isset($tmpSession)){
            $city = new City();
            $ipCountryRegionCity = $em->getRepository("CittandoSiteBundle:IpCountryRegionCity")->getIpCountryRegionCity($this->get('request')->server->get("REMOTE_ADDR"));
                if(isset($ipCountryRegionCity) && count($ipCountryRegionCity)!=0){
                    // Borrar solo para efectos de demo -- comentario en espannol
                    $session->set('cittandoCityBorrar', ' ['.$ipCountryRegionCity->getipCity().']');
                    $city = $em->getRepository("CittandoSiteBundle:City")->findByCityName($ipCountryRegionCity->getipCity());
                }else{
                    // Borrar solo para efectos de demo -- comentario en espannol
                    $session->set('cittandoCityBorrar', ' ['.$this->get('request')->server->get("REMOTE_ADDR").']');
                    $city = $em->getRepository("CittandoSiteBundle:City")->findByCityName('Venarotta');
                }

                if(isset($city) && count($city)!=0){
                    $cittandoCityRequest = array(
                        'cityId' => $city[0]->getId(),
                        'cityName' => $city[0]->getCityName(),
                        'cityLat' => $city[0]->getCityLat(),
                        'cityLong' => $city[0]->getCityLong(),
                        'countryCountry' => array('countryId' => $city[0]->getCountry()->getId(),
                                                    'countryName' => $city[0]->getCountry()->getName()
                                            ),
                        'provinceProvince' => array(
                            'provinceId' => $city[0]->getProvince()->getId(),
                            'provinceName' => $city[0]->getProvince()->getName(),
                            'provinceLat' => $city[0]->getProvince()->getLatitude(),
                            'provinceLong' => $city[0]->getProvince()->getLongitude()
                                            ),
                    );

                    $jsonEncode = new JsonEncode();
                    $cittandoCityRequest = $jsonEncode->encode($cittandoCityRequest,$format = 'json');
                    $session->set('cittandoCityRequest', $city[0]->getCityName());
                    $session->set('cittandoCityRequestAll', $cittandoCityRequest);
                }else{
                    $session->set('cittandoCityRequest', 'empty');
                }
        }

        $data['highlight'] = $em->getRepository("CittandoSiteBundle:City")->findBy(array('cityIsMetroarea'=> '1'),array('cityName' => 'ASC'),$limit=10);

        $data['events'] = (count($events) > 0)?array_slice($events,0,5):array();
        $data['venues'] = (count($venues) > 0)?array_slice($venues,0,5):array();
        $data['films'] = (count($films) > 0)?array_slice($films,0,5):array();

        $data['slider'] = array_merge(array('events'=>array_slice($events,-2,2)), array('venues'=>array_slice($venues,-2,2)),array('films'=>array_slice($films,-2,2)));
        //die(print_r($data['films']));
        return $data;
    }

    public function getTokenAction()
    {
        return new Response($this->container->get('form.csrf_provider')
            ->generateCsrfToken('authenticate'));
    }

    /**
     * @Route("/changecity/{cityid}", name="_changecity")
     * @Template()
     */
    public function changeCityAction($cityid){
        $request = $this->getRequest();
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager();

        $city = $em->getRepository("CittandoSiteBundle:City")->find($cityid);

        if(isset($city) && count($city)!=0){
            $cittandoCityRequest = array(
                'cityId' => $city->getId(),
                'cityName' => $city->getCityName(),
                'cityLat' => $city->getCityLat(),
                'cityLong' => $city->getCityLong(),
                'countryCountry' => array('countryId' => $city->getCountry()->getId(),
                    'countryName' => $city->getCountry()->getName()
                ),
                'provinceProvince' => array(
                    'provinceId' => $city->getProvince()->getId(),
                    'provinceName' => $city->getProvince()->getName(),
                    'provinceLat' => $city->getProvince()->getLatitude(),
                    'provinceLong' => $city->getProvince()->getLongitude()
                ),
            );

            $jsonEncode = new JsonEncode();
            $cittandoCityRequest = $jsonEncode->encode($cittandoCityRequest,$format = 'json');
            $session->set('cittandoCityRequest', $city->getCityName());
            $session->set('cittandoCityRequestAll', $cittandoCityRequest);
        }

        return $this->redirect($this->generateUrl('_cittando_home'));
    }
}
