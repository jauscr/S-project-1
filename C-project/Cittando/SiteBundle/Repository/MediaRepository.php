<?php

namespace Cittando\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Cittando\ApiBundle\Entity\Media;
use Doctrine\ORM\Query;

class MediaRepository extends EntityRepository
{
	/**
	 * Creates a new media row
	 * @param  array $data [key value with the fields and his values]
	 * @return Cittando\Apibundle\Entity\Media
	 */
	public function newMedia($data)
	{
		$em = $this->getEntityManager();
		$media = new Media();
		$now = new \DateTime();

		if(!empty($data['url']))
			$media->url = $data['url'];

		if(!empty($data['media_description']))
			$media->mediaDescription = $data['media_description'];

		if(!empty($data['media_alt_tag']))
			$media->mediaAltTag = $data['media_alt_tag'];

		$media->created = $now;

		if (!empty($data['file'])) {
			$media->file = $data['file'];
			$media->url = null;
			$media->processFile();
		}

		if (!empty($data['event'])) {
			$events = new \Doctrine\Common\Collections\ArrayCollection();
			$events[] = is_int($data['event']) ? $em->getRepository("CittandoSiteBundle:Event")->find($data['event']) : $data['event'];

			$media->event = $events;
		}

		$em->persist($media);
		$em->flush();

		return $media;
	}

	/**
	 * Fetch a media row
	 * @param  int $mediaId
	 * @return Cittando\ApiBundle\Entity\Media
	 */
	public function getMedia($mediaId)
	{
		$dql = 
			"SELECT m FROM CittandoSiteBundle:Media m
			WHERE m.mediaId = :id";

		$query = $this->getEntityManager()->createQuery($dql);
		$query->setParameter("id", $mediaId);

		return $query->getSingleResult();
	}
}