<?php

namespace fcm\manager\models;

interface DeviceRegisterInterface
{
    /**
     * Register device info to database method.
     *
     * @param array|string $deviceToken
     * @param int $user_id
     * @return bool
     */
    public static function registerDevice($deviceTokens, int $user_id): bool;
}