<?php

namespace Soound\AppBundle\Twig;

class SooundExtension extends \Twig_Extension
{
    public function getFilters(){
        return array(
            new \Twig_SimpleFilter('daysLeft', array($this, 'daysLeft')),
        );
    }

    public function daysLeft($date)
    {
        $datetime1 = new \DateTime();
        $datetime2 = new \DateTime($date);

        $start = strtotime($datetime1->format('m/d/Y'));
        $end = strtotime($datetime2->format('m/d/Y'));
        $difference = $end - $start;
        $daysLeft = round($difference / 86400);

        return $daysLeft;
    }

    public function getName()
    {
        return 'soound_extension';
    }
}