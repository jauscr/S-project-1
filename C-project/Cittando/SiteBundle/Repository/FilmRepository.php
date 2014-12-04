<?php

namespace Cittando\SiteBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class FilmRepository extends EntityRepository
{
    /**
     * Return a list of film, promoted first
     * @param  array $filters (limit, page)
     * @return Film ArrayCollection
     */
    public function getPopular($filters = null)
    {
        $filters['limit'] = isset($filters['limit']) ? $filters['limit'] : 8;
        $result = array();

        $qb = "SELECT f, p, v, m, mCat,cat, ct FROM CittandoSiteBundle:Film f
                LEFT JOIN f.promoted p
                LEFT JOIN f.venue v
                INNER JOIN f.media m
                LEFT JOIN m.category mCat
                LEFT JOIN f.category cat
                LEFT JOIN cat.type ct
                ORDER BY f.promoted DESC";

        $query = $this->getEntityManager()->createQuery($qb);
        $query->setMaxResults($filters['limit']);
        //die(print_r($query->setMaxResults($filters['limit'])->getArrayResult()));

        if (isset($filters['page'])) {
            $query->setFirstResult(($filters['page'] - 1) * $filters['limit'])->getArrayResult();
            $result['films'] = new Paginator($query, false);
            $result['count'] = count($result['films']);
            $result['pages'] = ceil($result['count'] / $filters['limit']);
        } else {
            $result = $query->getArrayResult();
        }

        return $result;
    }

    /**
     * Creates a new film entity with his relationships
     * @param  array $filmData [posted data]
     * @return Film
     */
    public function newFilm($filmData)
    {
        $em = $this->getEntityManager();

        // basic fields on the entity
        $defaultValidFields = array(
            'title',
            'synopsis',
            'director',
            'cast',
            'writers',
            'productionCo',
            'language',
            'websiteUrl',
            'releaseDateUsa',
            'releaseDateItaly',
            'releaseDateOther'
        );

        // Film entity
        $film = new Film();

        // interate through all default fields to extract the values
        foreach ($defaultValidFields as $field) {
            $film->$field = !empty($filmData[$field]) ? $filmData[$field] : null;
        }

        // set the category relationship
        if (!empty($filmData['category'])) {
            $category = $em->getRepository("CittandoSiteBundle:Category")->find($filmData['category']);

            if (!empty($category))
                $film->category = $category;
        }

        // set the venue relationship
        if (!empty($filmData['venue'])) {
            $venue = $em->getRepository('CittandoSiteBundle:Venue')->find($filmData['venue']);

            if (!empty($venue))
                $film->venue[] = $venue;
        }

        // set the film media
        if (!empty($filmData['media'])) {
            $film->media[] = $filmData['media'];
        }

        $em->persist($film);
        $em->flush();

        return $film;
    }

    public function getFilm($filmId)
    {
        $dql =
            "SELECT f, p, v, m, mCat, cat , ct FROM CittandoSiteBundle:Film f
            LEFT JOIN f.promoted p
            LEFT JOIN f.venue v
            LEFT JOIN f.media m
            LEFT JOIN m.category mCat
            LEFT JOIN f.category cat
            LEFT JOIN cat.type ct
            WHERE f.id = :id";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("id", $filmId);
        //die(print_r($query->getArrayResult()));
        return $query->getArrayResult();
    }

    public function getComingSoon(){
        $qb = "SELECT f, p, v, m FROM CittandoSiteBundle:Film f
                LEFT JOIN f.promoted p
                LEFT JOIN f.venue v
                LEFT JOIN f.media m
                WHERE  f.releaseDateUsa > CURRENT_DATE()
                ORDER BY f.releaseDateUsa";

        $query = $this->getEntityManager()->createQuery($qb);
        return $query->getArrayResult();
    }

    public function getShowtimes($filmId, $venueId){
        $dql ="SELECT e, v FROM CittandoSiteBundle:FilmVenueRel e
               LEFT JOIN e.venueVenue v
               WHERE e.filmFilm = :filmId
               AND e.venueVenue = :venueId";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("filmId", $filmId);
        $query->setParameter("venueId", $venueId);
        return $query->getArrayResult();
    }
}