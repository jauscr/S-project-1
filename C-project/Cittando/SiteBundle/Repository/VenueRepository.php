<?php

namespace Cittando\SiteBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Cittando\SiteBundle\Entity\Venue;

class VenueRepository extends EntityRepository
{
    /**
     * Return popular venue promoted at first
     * and venue close to start
     * @param  array $filters
     * @return array
     */
    public function getPopular($filters)
    {
        $filters['limit'] = (isset($filters['limit'])) ? $filters['limit'] : 7;
        $result = null;

        $dql = "SELECT e, p, m,ci,co FROM CittandoSiteBundle:Venue e
                 LEFT JOIN e.promoted p
                 LEFT JOIN e.media m
                 LEFT JOIN e.city ci
                 LEFT JOIN e.country co
                 ORDER BY e.promoted DESC";

        $query = $this->getEntityManager()->createQuery($dql);

        //die($query->getSQL());

        $query->setMaxResults($filters['limit']);

        // use pagination isteadh
        if (isset($filters['page'])) {
            $query->setFirstResult(($filters['page'] - 1) * $filters['limit'])->getArrayResult();

            $result['venues'] = new Paginator($query, false);
            $result['count'] = count($result['venues']);
            $result['pages'] = ceil($result['count'] / $filters['limit']);
        } else {
            $result = $query->getArrayResult();
        }

        return $result;
    }

    /**
     * Return a venue by his id
     * @param  int $id
     * @return Entity Object
     */
    public function getVenue($id)
    {
        $dql =
            "SELECT v, p,e,f,cat,ci,co FROM CittandoSiteBundle:Venue v
            LEFT JOIN v.promoted p
            LEFT JOIN v.event e
            LEFT JOIN v.film f
            LEFT JOIN v.category cat
            LEFT JOIN v.city ci
            LEFT JOIN v.country co
            WHERE v.id = :id";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("id", $id);
        return $query->getArrayResult();
    }

    /**
     * Get a list of Venues per user
     *
     * @param $userId
     * @return array Entity Object
     */
    public function getVenuesByUser($userId)
    {
        $dql = "SELECT p, e, v, cit, co from CittandoSiteBundle:UserVenueSaved p
                  LEFT JOIN p.venueVenue e
                  LEFT JOIN e.event v
                  LEFT JOIN e.city cit
                  LEFT JOIN e.country co
                  where p.userUser = :id";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("id", $userId);

        return $query->getArrayResult();
    }
}