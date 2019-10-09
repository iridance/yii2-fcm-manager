<?php

namespace fcm\manager\models;

interface NotificationInterface
{
    /**
     * Customize status value for send success.
     *
     * @return string|int
     */
    public function getSuccessStatus();

    /**
     * Customize status value for send fail.
     *
     * @return string|int
     */
    public function getFailStatus();

    /**
     * Save status to database
     *
     * @param string|int $status
     * @return void
     */
    public function updateStatus($status);
}