<?php

namespace Cittando\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Cittando\ApiBundle\Entity\Category;

class CategoryRepository extends EntityRepository
{

    /**
     * Get a list of categories filtered by the type
     * @param  array $type (i.e event, venue, cinema)
     * @return Catengory Resultset
     */
    public function getCategoriesByType($type)
    {
        $type = is_array($type) ? $type : array($type);

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("c")
            ->from('CittandoSiteBundle:Category', 'c')
            ->leftJoin("c.type", 't')
            ->where('t.name IN (:type)')
            ->orderBy('c.name', 'ASC');

        $query = $qb->getQuery();
        $query->setParameter("type", $type);

        return $query->getResult();
    }
}