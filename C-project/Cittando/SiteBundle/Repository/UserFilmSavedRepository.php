<?php

namespace Cittando\SiteBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Cittando\SiteBundle\Entity\UserFilmSaved;

class UserFilmSavedRepository extends EntityRepository
{
    /**
     * get film by id
     * @param  int $userId
     * @param  int $filmId
     * @return array
     */

    public function getUserFilmSaved($userId,$filmId){
        $dql=
            "select e from CittandoSiteBundle:UserFilmSaved e
            where e.userUser = :idUser
            and e.filmFilm = :idFilm";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("idUser", $userId);
        $query->setParameter("idFilm", $filmId);

        return $query->getSingleResult();
    }

    /**
     * get film by id
     * @param  int $userId
     * @param  int $filmId
     * @return array
     */

    public function getUserFilmSavedArray($userId,$filmId){
        $dql=
            "select e, u, f from CittandoSiteBundle:UserFilmSaved e
            LEFT JOIN e.userUser u
            LEFT JOIN e.filmFilm f
            where e.userUser = :idUser
            and e.filmFilm = :idFilm";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("idUser", $userId);
        $query->setParameter("idFilm", $filmId);

        return $query->getArrayResult();
    }

    /**
     * Get a list of films per user
     *
     * @param $userId
     * @return array Entity Object
     */
    public function getFilmsByUser($userId)
    {
        $dql=
            "select e, u, f, v from CittandoSiteBundle:UserFilmSaved e
            LEFT JOIN e.userUser u
            LEFT JOIN e.filmFilm f
            LEFT JOIN f.venue v
            Where e.userUser = :userId";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("userId", $userId);

        return $query->getArrayResult();
    }
}