<?php

namespace Cittando\SiteBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

// use Knp\Menu\ItemInterface as MenuItemInterface;

use Cittando\SiteBundle\Entity\Artist;

class ArtistAdmin extends Admin
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
            ->add('category')
            ->add('status')
            ->add('promoted')
            ->add('bio', null, array("required" => false))
            ->add('websiteUrl', null, array("required" => false))
            ->add('websiteUrl2', null, array("required" => false))
            ->add('facebookUrl', null, array("required" => false))
            ->add('twitterUsername', null, array("required" => false));
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
            ->add('category')
            ->add('status')
            ->add('promoted')
            ->add('bio', null, array("required" => false))
            ->add('websiteUrl', null, array("required" => false))
            ->add('websiteUrl2', null, array("required" => false))
            ->add('facebookUrl', null, array("required" => false))
            ->add('twitterUsername', null, array("required" => false))
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
            ->add('category')
            ->add('status')
            ->add('promoted')
            ->add('bio', null, array("required" => false))
            ->add('websiteUrl', null, array("required" => false))
            ->add('websiteUrl2', null, array("required" => false))
            ->add('facebookUrl', null, array("required" => false))
            ->add('twitterUsername', null, array("required" => false))
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
            ->add('category')
            ->add('status')
            ->add('promoted')
            ->add('bio', null, array("required" => false))
            ->add('websiteUrl', null, array("required" => false))
            ->add('websiteUrl2', null, array("required" => false))
            ->add('facebookUrl', null, array("required" => false))
            ->add('twitterUsername', null, array("required" => false));
    }
}