<?php

namespace Cittando\SiteBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

// use Knp\Menu\ItemInterface as MenuItemInterface;

use Cittando\SiteBundle\Entity\City;

class CityAdmin extends Admin
{

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('cityName')
            ->add('country')
            ->add('province')
            ->add('cityNameAlt', null, array("required" => false))
            ->add('cityLat', null, array("required" => false))
            ->add('cityLong', null, array("required" => false))
            ->add('geoAccuracy', null, array("required" => false))
            ->add('cityIsMetroarea', null, array("required" => false))
            ->add('cityTimezone', null, array("required" => false))
            ->add('cityPopulation', null, array("required" => false))
            ->add('cityMetroarea', null, array("required" => false));
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
            ->add('cityName')
            ->add('country')
            ->add('province')
            ->add('cityNameAlt', null, array("required" => false))
            ->add('cityLat', null, array("required" => false))
            ->add('cityLong', null, array("required" => false))
            ->add('geoAccuracy', null, array("required" => false))
            ->add('cityIsMetroarea', null, array("required" => false))
            ->add('cityTimezone', null, array("required" => false))
            ->add('cityPopulation', null, array("required" => false))
            ->add('cityMetroarea', null, array("required" => false))
            ->end();
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('cityName')
            ->add('country')
            ->add('province')
            ->add('cityNameAlt', null, array("required" => false))
            ->add('cityLat', null, array("required" => false))
            ->add('cityLong', null, array("required" => false))
            ->add('geoAccuracy', null, array("required" => false))
            ->add('cityIsMetroarea', null, array("required" => false))
            ->add('cityTimezone', null, array("required" => false))
            ->add('cityPopulation', null, array("required" => false))
            ->add('cityMetroarea', null, array("required" => false))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'view' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('cityName')
            ->add('country')
            ->add('province')
            ->add('cityNameAlt', null, array("required" => false))
            ->add('cityLat', null, array("required" => false))
            ->add('cityLong', null, array("required" => false))
            ->add('geoAccuracy', null, array("required" => false))
            ->add('cityIsMetroarea', null, array("required" => false))
            ->add('cityTimezone', null, array("required" => false))
            ->add('cityPopulation', null, array("required" => false))
            ->add('cityMetroarea', null, array("required" => false));
    }
}