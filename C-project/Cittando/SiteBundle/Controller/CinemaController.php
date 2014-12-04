<?php

namespace Cittando\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Cittando\SiteBundle\Entity\UserFilmSaved;

class CinemaController extends Controller
{
    /**
     * @Route("/cinema", name="_cinema")
     * @Template()
     */
    public function indexAction()
    {
        $filters = $this->getRequest()->query->all();
        $filters['limit'] = (isset($filters['limit'])) ? $filters['limit'] : 8;
        $filters['page'] = (isset($filters['page'])) ? $filters['page'] : 1;
        $em = $this->getDoctrine()->getManager();

        if ($this->container->get('request')->isXmlHttpRequest()) {
            // for jquery request

        } else {
            //Normal  /events request
            $data = $em->getRepository("CittandoSiteBundle:Film")->getPopular($filters);
            $data = empty($data) ? array() : $data;
            $data['slider'] = (count($data) > 0) ? array_slice($data, 0, 5) : array();

            $CommingSoon = $em->getRepository("CittandoSiteBundle:Film")->getComingSoon();
            $data['movies']= array_chunk($CommingSoon, 3);
            $data = array_merge($filters, $data);
        }
        $data['highlight'] = $em->getRepository("CittandoSiteBundle:City")->findBy(array('cityIsMetroarea'=> '1'),array('cityName' => 'ASC'),$limit=10);
        return $data;
    }

    /**
     * @Route("/cinema/{id}", name="_cinema_id")
     * @Template()
     */
    public function filmDetailAction($id)
    {
        $t=0; $p=0; $e=0;
        $em = $this->getDoctrine()->getManager();
        // get if the cinema is saved in this logged user
        $user = $this->get('security.context')->getToken()->getUser();
        if ($user != 'anon.')
        {
            $result = $em->getRepository('CittandoSiteBundle:UserFilmSaved')->getUserFilmSavedArray($user->getId(),$id);
            for($e=0; $e< count($result); $e++){
                $savedFilms[$e] =  $result[$e]['filmFilm']['id'];
            }
            $data['isSaved'] = empty($savedFilms) ? array() : $savedFilms;
        }
        else
        {
            $data['isSaved'] = array();
        }

        $data['films'] = $em->getRepository("CittandoSiteBundle:Film")->getFilm($id);

        // re-order media information to be more simple manage array on twig
        if (count($data['films'][0]['media']) > 0){
            $media = $data['films'][0]['media'];
            unset($data['films'][0]['media']);
            foreach($media as $value){
                if($value['category']['name'] == 'trailer'){
                    $data['media']['trailers'][$t]=$value;
                    $t++;
                }
                if($value['category']['name'] == 'picture'){
                    $data['media']['picture'][$p]=$value;
                    $p++;
                }
            }
            // make group of pictures 3 for slider images
            $data['media']['picture']= array_chunk($data['media']['picture'], 3);
        }
        else{
            $data['media']=array();
        }

        //die(print_r($data));
        return $this->render("CittandoSiteBundle:Cinema:film_detail.html.twig",$data);
    }

    /**
     * @Route("/cinema/{id}/showtimes", name="_showtimes")
     * @Template()
     */
    public function filmShowtimesAction($id)
    {
        $t=0; $p=0;
        $em = $this->getDoctrine()->getManager();
        // get if thi cinema is saved in this logged user
        $user = $this->get('security.context')->getToken()->getUser();
        if ($user != 'anon.')
        {
            $result = $em->getRepository('CittandoSiteBundle:UserFilmSaved')->getUserFilmSavedArray($user->getId(),$id);
            for($e=0; $e< count($result); $e++){
                $savedFilms[$e] =  $result[$e]['filmFilm']['id'];
            }
            $data['isSaved'] = empty($savedFilms) ? array() : $savedFilms;
        }
        else
        {
            $data['isSaved'] = array();
        }
        $data['films'] = $em->getRepository("CittandoSiteBundle:Film")->getFilm($id);
        //$data['showtimes'] = $em->getRepository("")->getShowtimes($id);
        // re-order media information to be more simple manage array on twig
        if (count($data['films'][0]['media']) > 0){
            $media = $data['films'][0]['media'];
            unset($data['films'][0]['media']);
            foreach($media as $value){
                if($value['category']['name'] == 'trailer'){
                    $data['media']['trailers'][$t]=$value;
                    $t++;
                }
                if($value['category']['name'] == 'picture'){
                    $data['media']['picture'][$p]=$value;
                    $p++;
                }
            }
            // make group of pictures 3 for slider images
            $data['media']['picture']= array_chunk($data['media']['picture'], 3);
        }
        else{
            $data['media']=array();
        }
        return $this->render("CittandoSiteBundle:Cinema:film_showtimes.html.twig",$data);
    }


    /**
     * @Route("/cinema/{filmId}/showtime_desc/{venueId}", name="_showtime_desc")
     *
     */
    public function showtimeDescAction($filmId,$venueId)
    {
        if ($this->container->get('request')->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $e = 0;
            $user = $this->get('security.context')->getToken()->getUser();

            if ($user != 'anon.') {
                $result['savedVenues'] = $em->getRepository('CittandoSiteBundle:Venue')->getVenuesByUser($user->getId());
                foreach ($result['savedVenues'] as $id) {
                    $savedVenue[$e] = $id['venueVenue']['id'];
                    $e++;
                }
                $data['isSaved'] = empty($savedVenue) ? array() : $savedVenue;
            }

            $data['showtimes'] = $em->getRepository("CittandoSiteBundle:Film")->getShowtimes($filmId,$venueId);

            $response = json_encode($data);
            return new Response($response);
        }
        else{
            return array();
        }

    }

    /**
     * Get user and event ID clicked
     * @Route("/cinema/save/{filmId}", name="_save_cinema")
     * @return Response
     */
    public function saveFilmAction($filmId)
    {
        if ($this->container->get('request')->isXmlHttpRequest()) {
            $setData = new UserFilmSaved();
            $user = $this->get('security.context')->getToken()->getUser(); //Get current User Info

            if (is_numeric($user->getId())) {
                //find object information using parameter $filmId
                $Film = $this->getDoctrine()
                    ->getRepository('CittandoSiteBundle:Film')
                    ->find($filmId);

                //find object information using parameter Current User $user
                $UserId = $this->getDoctrine()
                    ->getRepository('CittandoSiteBundle:User')
                    ->find($user->getId());

                //Save info
                $setData->setFilmFilm($Film);
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
    }

    /**
     * Delete User film on click
     * @Route("/cinema/delete/{FilmId}", name="_delete_film")
     * @return Response
     */

    public function deleteFilmUserAction($FilmId)
    {
        $user = $this->get('security.context')->getToken()->getUser(); //Get current User Info
        if ($this->container->get('request')->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $result = $em->getRepository('CittandoSiteBundle:UserFilmSaved')->getUserFilmSaved($user->getId(), $FilmId);
            $em->remove($result);
            $em->flush();
            return new Response(json_encode(true));
        }
    }
}
