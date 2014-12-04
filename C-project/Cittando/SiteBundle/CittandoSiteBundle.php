<?php

namespace Cittando\SiteBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CittandoSiteBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}