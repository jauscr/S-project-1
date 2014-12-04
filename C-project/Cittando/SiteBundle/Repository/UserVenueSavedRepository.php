<?php

namespace Cittando\SiteBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Cittando\SiteBundle\Entity\UserVenueSaved;

class UserVenueSavedRepository extends EntityRepository
{
    /**
     * Delete venue user by id
     * @param  int $userId
     * @param  int $venueId
     * @return array
     */

    public function getUserVenueSaved($userId, $venueId)
    {
        $dql =
            "select e from CittandoSiteBundle:UserVenueSaved e
             where e.userUser = :idUser
             and e.venueVenue = :idVenue";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("idUser", $userId);
        $query->setParameter("idVenue", $venueId);

        return $query->getSingleResult();
    }

    /**
     * @param  int $userId
     * @param  int $venueId
     * @return array
     */

    public function getUserVenueSavedArray($userId,$venueId){
        $dql=
            "select e, u, v from CittandoSiteBundle:UserVenueSaved e
            LEFT JOIN e.userUser u
            LEFT JOIN e.venueVenue v
            where e.userUser = :userId
            and e.venueVenue = :venueId";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("userId", $userId);
        $query->setParameter("venueId", $venueId);

        return $query->getArrayResult();
    }
}