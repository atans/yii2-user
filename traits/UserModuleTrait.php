<?php

namespace atans\user\traits;

use atans\user\Module;

trait UserModuleTrait
{
    /**
     * @return Module
     */
    public static function getUserModule()
    {
        static $userModule = null;

        if (is_null($userModule)) {
            $userModule = Module::getInstance();
        }

        return $userModule;
    }
}