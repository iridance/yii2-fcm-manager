<?php

namespace fcm\manager\models;

interface DeviceRegisterInterface
{
    /**
     * Register device info to database method.
     *
     * @param array|string $deviceTokens
     * @param int $user_id
     * @return bool
     */
    public static function registerDevice($deviceTokens, int $user_id): bool;

    /**
     * Unregister device info method.
     *
     * @param array|string $deviceTokens
     * @return boolean
     */
    public static function unRegisterDevice($deviceTokens): bool;
}