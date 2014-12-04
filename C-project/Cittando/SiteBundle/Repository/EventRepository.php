<?php

namespace Cittando\SiteBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Cittando\SiteBundle\Entity\Event;

class EventRepository extends EntityRepository
{
    /**
     * Return popular events promoted at first
     * and events close to start
     * @param  array $filters
     * @return array
     */
    public function getPopular($filters)
    {
        $filters['limit'] = (isset($filters['limit'])) ? $filters['limit'] : 7;
        $result = null;

        $dql = "SELECT e, p, v, m FROM CittandoSiteBundle:Event e
                LEFT JOIN e.promoted p
                LEFT JOIN e.venue v
                LEFT JOIN e.media m
                ORDER BY e.promoted DESC";

        // original -> ORDER BY e.promoted DESC, e.startDate DESC

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setMaxResults($filters['limit']);

        // use pagination instead
        if (isset($filters['page'])) {
            $query->setFirstResult(($filters['page'] - 1) * $filters['limit'])->getArrayResult(); // add this line in the end ->getArrayResult()
            $result['events'] = new Paginator($query, false);
            $result['count'] = count($result['events']);
            $result['pages'] = ceil($result['count'] / $filters['limit']);

        } else {

            $result = $query->getArrayResult();

        }
        return $result;
    }

    /**
     * Get a list of events per user
     *
     * @param $userId
     * @return array Entity Object
     */
    public function getEventsByUser($userId)
    {
        $dql = "SELECT p, e, v, cit, cont from CittandoSiteBundle:UserEventSaved p
              LEFT JOIN p.eventEvent e
              LEFT JOIN e.venue v
              LEFT JOIN v.city cit
              LEFT JOIN v.country cont
              WHERE p.userUser = :id";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("id", $userId);

        return $query->getArrayResult();
    }

    /**
     * Return a event by his id
     * @param  int $id
     * @return Entity Object
     */
    public function getEvent($id)
    {
        $dql =
            "SELECT e, p, v, m, cat,cit,cont FROM CittandoSiteBundle:Event e
            LEFT JOIN e.promoted p
            LEFT JOIN e.venue v
            LEFT JOIN e.media m
            LEFT JOIN e.category cat
            LEFT JOIN v.city cit
            LEFT JOIN v.country cont
            WHERE e.id = :id";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter("id", $id);

        return $query->getArrayResult();
    }

    /**
     * Creates a new event entity
     * @param  array $data [key value with the fields of the event entity]
     * @return Cittando\ApiBundle\Entity\Event
     */
    public function newEvent($data)
    {
        $em = $this->getEntityManager();
        $event = new Event();

        if (!empty($data['title'])) {
            $event->title = $data['title'];
        }
        if (!empty($data['description'])) {
            $event->description = $data['description'];
        }
        if (!empty($data['ticket_url'])) {
            $event->ticketUrl = $data['ticket_url'];
        }
        if (!empty($data['status'])) {
            $event->status = $em->getRepository("CittandoSiteBundle:Status")->find(1);
        }
        if (!empty($data['start_date'])) {
            $event->startDate = new \DateTime($data['start_date']);
        }
        if (!empty($data['end_date'])) {
            $event->endDate = new \DateTime($data['end_date']);
        }

        if (!empty($data['venue'])) {
            $venue = $em->getRepository("CittandoSiteBundle:Venue")->find($data['venue']);

            if (!empty($venue))
                $venues = new \Doctrine\Common\Collections\ArrayCollection();
            $venues[] = $venue;
            $event->venue = $venues;
        }

        if (!empty($data['category'])) {
            $category = $em->getRepository("CittandoSiteBundle:Category")->find($data['category']);

            if (!empty($category))
                $event->category = $category;
        }

        if (!empty($data['media'])) {
            $media = new \Doctrine\Common\Collections\ArrayCollection();
            $media[] = $data['media'];

            $event->media = $media;
        }

        $em->persist($event);
        $em->flush();

        return $event;
    }


    /**
     * Return popular events promoted at first
     * and events close to start to use in the map
     * @return array
     */
    public function getPopularToMap()
    {
        $dql = "SELECT e, p, v, m FROM CittandoSiteBundle:Event e
                LEFT JOIN e.promoted p
                LEFT JOIN e.venue v
                LEFT JOIN e.media m
                ORDER BY e.promoted DESC";

        // original -> ORDER BY e.promoted DESC, e.startDate DESC

        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getArrayResult();
    }
}