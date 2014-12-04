<?php

namespace Soound\AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SooundAppBundle extends Bundle
{
    public function getParent(){
        return 'FOSUserBundle';
    }
}