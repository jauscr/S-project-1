<?php

namespace Cittando\SiteBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;

class AuthorizeFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('allowAccess', 'checkbox', array(
            'label' => 'Allow access',
        ));
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Cittando\SiteBundle\Form\Model\Authorize');
    }

    public function getName()
    {
        return 'cittando_oauth_server_authorize';
    }

}  