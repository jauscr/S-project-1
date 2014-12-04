<?php

namespace Cittando\SiteBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('firstName');
        $builder->add('lastName');
        // add terms of service
        $builder->add("tos", "checkbox", array(
            'property_path' => false,
            'required' => true
        ));

    }

    public function getName()
    {
        return 'cittando_user_registration';
    }
}