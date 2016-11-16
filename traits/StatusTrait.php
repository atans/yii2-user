<?php

namespace atans\user\traits;

/**
 * StatusTrait
 *
 * @property string $status
 */
trait StatusTrait
{
    /**
     * Status exits
     *
     * @param string $status
     * @return bool
     */
    public static function statusExits($status)
    {
        if (in_array($status, self::getStatuses())) {
            return true;
        }

        return false;
    }

    /**
     * Get status name
     *
     * @param null|string $status
     * @return string|null
     */
    public function getStatusName($status = null)
    {
        $status = $status ? $status : $this->status;

        $statusValueOptions = self::getStatusValueOptions();
        if (isset($statusValueOptions[$status])) {
            return $statusValueOptions[$status];
        }

        return null;
    }

    /**
     * Get statuses
     *
     * @return array
     */
    public static function getStatuses()
    {
        return array_keys(self::getStatusValueOptions());
    }

    /**
     * Get status value options
     *
     * @return array
     */
    public static function getStatusValueOptions()
    {
        return [];
    }
}