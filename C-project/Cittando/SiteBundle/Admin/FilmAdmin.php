<?php

namespace Cittando\SiteBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

// use Knp\Menu\ItemInterface as MenuItemInterface;

use Cittando\SiteBundle\Entity\Film;

class FilmAdmin extends Admin
{

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('venue')
            ->add('category')
            ->add('status')
            ->add('promoted')
            ->add('synopsis', null, array("required" => false))
            ->add('director', null, array("required" => false))
            ->add('cast', null, array("required" => false))
            ->add('writers', null, array("required" => false))
            ->add('productionCo', null, array("required" => false))
            ->add('language', null, array("required" => false))
            ->add('websiteUrl', null, array("required" => false))
            ->add('releaseDateUsa', null, array("required" => false))
            ->add('releaseDateItaly', null, array("required" => false))
            ->add('releaseDateOther', null, array("required" => false));
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
            ->add('title')
            ->add('venue')
            ->add('category')
            ->add('status')
            ->add('promoted')
            ->add('synopsis', null, array("required" => false))
            ->add('director', null, array("required" => false))
            ->add('cast', null, array("required" => false))
            ->add('writers', null, array("required" => false))
            ->add('productionCo', null, array("required" => false))
            ->add('language', null, array("required" => false))
            ->add('websiteUrl', null, array("required" => false))
            ->add('releaseDateUsa', null, array("required" => false))
            ->add('releaseDateItaly', null, array("required" => false))
            ->add('releaseDateOther', null, array("required" => false))
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
            ->add('title')
            ->add('venue')
            ->add('category')
            ->add('status')
            ->add('promoted')
            ->add('synopsis', null, array("required" => false))
            ->add('director', null, array("required" => false))
            ->add('cast', null, array("required" => false))
            ->add('writers', null, array("required" => false))
            ->add('productionCo', null, array("required" => false))
            ->add('language', null, array("required" => false))
            ->add('websiteUrl', null, array("required" => false))
            ->add('releaseDateUsa', null, array("required" => false))
            ->add('releaseDateItaly', null, array("required" => false))
            ->add('releaseDateOther', null, array("required" => false))
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
            ->add('title')
            ->add('venue')
            ->add('category')
            ->add('status')
            ->add('promoted')
            ->add('synopsis', null, array("required" => false))
            ->add('director', null, array("required" => false))
            ->add('cast', null, array("required" => false))
            ->add('writers', null, array("required" => false))
            ->add('productionCo', null, array("required" => false))
            ->add('language', null, array("required" => false))
            ->add('websiteUrl', null, array("required" => false))
            ->add('releaseDateUsa', null, array("required" => false))
            ->add('releaseDateItaly', null, array("required" => false))
            ->add('releaseDateOther', null, array("required" => false));
    }
}