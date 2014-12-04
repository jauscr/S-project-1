<?php

namespace Cittando\SiteBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

// use Knp\Menu\ItemInterface as MenuItemInterface;

use Cittando\SiteBundle\Entity\Event;

class EventAdmin extends Admin
{

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('eventTitle')
            ->add('eventDescription')
            ->add('startDate')
            ->add('endDate')
            ->add('category')
            ->add('status')
            ->add('venue')
            ->add('promoted')
            ->add('ticketUrl', null, array("required" => false))
            ->add('media', null, array("required" => false));
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
            ->add('eventTitle')
            ->add('eventDescription')
            ->add('startDate')
            ->add('endDate')
            ->add('category')
            ->add('status')
            ->add('venue')
            ->add('promoted')
            ->add('ticketUrl', null, array("required" => false))
            ->add('media', null, array("required" => false))
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
            ->add('eventTitle')
            ->add('eventDescription')
            ->add('startDate')
            ->add('endDate')
            ->add('category')
            ->add('status')
            ->add('venue')
            ->add('promoted')
            ->add('ticketUrl', null, array("required" => false))
            ->add('media', null, array("required" => false))
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
            ->add('eventTitle')
            ->add('eventDescription')
            ->add('startDate')
            ->add('endDate')
            ->add('category')
            ->add('status')
            ->add('venue')
            ->add('promoted')
            ->add('ticketUrl', null, array("required" => false))
            ->add('media', null, array("required" => false));
    }
}