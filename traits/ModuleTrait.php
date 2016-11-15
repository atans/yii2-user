<?php

namespace atans\user\traits;

use atans\user\Module;

trait ModuleTrait
{
    /**
     * @return Module
     */
    public function getModule()
    {
        return Module::getInstance();
    }
}