<?php

namespace Cittando\SiteBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CityRepository extends EntityRepository
{

    /**
     * Get a Object by IP address of City
     * @param $cityName
     * @return \Cittando\SiteBundle\Entity\City
     */
    public function findByCityName($cityName)
    {

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("c")
            ->from('CittandoSiteBundle:City', 'c')
            ->where('c.cityName = :cityName');

        $query = $qb->getQuery();
        $query->setParameter("cityName", $cityName);
        $result = $query->getSingleResult();
        return $result;
    }

    /*
     * Get list of cities for select on home page
     * @param $limit
     * @return \Cittando\SiteBundle\Entity\City ArrayCollection
     */
    public function findHighlightCities($limit = null){
        /*$dql = $this->getEntityManager()->createQueryBuilder();
        $dql->select("c")
            ->from('CittandoSiteBundle:City', 'c')
            ->where('c.cityIsMetroarea = 1');

        if (false === is_null($limit)){
            $dql->setMaxResults($limit);
        }else{
            $dql->setMaxResults(10);
        }

        $query = $dql->getQuery();
        return $query->getResult();*/
        return $this->findBy(array('cityIsMetroarea'=> '1'));
    }
}