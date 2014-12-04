<?php

namespace Cittando\SiteBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Cittando\SiteBundle\Entity\FilmVenueRel;

class FilmVenueRelRepository extends EntityRepository
{
    /**
     * Get all films in venue
     * @param  int $venueId
     * @return array
     */

    public function getAllFilmsInVenue($venueId,$timeId)
    {
        $today =
            "select e, f from CittandoSiteBundle:FilmVenueRel e
             LEFT JOIN e.filmFilm f
             where e.venueVenue = :idVenue
             and CURRENT_DATE() BETWEEN e.dateFrom AND e.dateTo";

        $thisWeek =
            "select e, f from CittandoSiteBundle:FilmVenueRel e
             LEFT JOIN e.filmFilm f
             where e.venueVenue = :idVenue
             and CURRENT_DATE() BETWEEN e.dateFrom AND e.dateTo";

        $query = $this->getEntityManager()->createQuery($today);
        $query->setParameter("idVenue", $venueId);

        return $query->getArrayResult();
    }

}