<?php

namespace Cittando\SiteBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

// use Knp\Menu\ItemInterface as MenuItemInterface;

use Cittando\SiteBundle\Entity\Province;

class ProvinceAdmin extends Admin
{

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('country')
            ->add('code', null, array("required" => false))
            ->add('codeIso', null, array("required" => false))
            ->add('level', null, array("required" => false))
            ->add('latitude', null, array("required" => false))
            ->add('longitude', null, array("required" => false))
            ->add('geoAccuracy', null, array("required" => false));
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
            ->add('name')
            ->add('country')
            ->add('code', null, array("required" => false))
            ->add('codeIso', null, array("required" => false))
            ->add('level', null, array("required" => false))
            ->add('latitude', null, array("required" => false))
            ->add('longitude', null, array("required" => false))
            ->add('geoAccuracy', null, array("required" => false))
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
            ->add('name')
            ->add('country')
            ->add('code', null, array("required" => false))
            ->add('codeIso', null, array("required" => false))
            ->add('level', null, array("required" => false))
            ->add('latitude', null, array("required" => false))
            ->add('longitude', null, array("required" => false))
            ->add('geoAccuracy', null, array("required" => false))
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
            ->add('name')
            ->add('country')
            ->add('code', null, array("required" => false))
            ->add('codeIso', null, array("required" => false))
            ->add('level', null, array("required" => false))
            ->add('latitude', null, array("required" => false))
            ->add('longitude', null, array("required" => false))
            ->add('geoAccuracy', null, array("required" => false));
    }
}