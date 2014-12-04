<?php

namespace Cittando\SiteBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Cittando\SiteBundle\Entity\UserEventSaved;

class UserEventSavedRepository extends EntityRepository
{
    /**
    * Delete event user by id
    * @param  int $userId
    * @param  int $eventId
    * @return array
    */

    public function getUserEventSaved($userId,$eventId){
        $dql=
            "select e from CittandoSiteBundle:UserEventSaved e
            where e.userUser = :idUser
            and e.eventEvent = :idEvent";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("idUser", $userId);
        $query->setParameter("idEvent", $eventId);

        return $query->getSingleResult();
    }
}