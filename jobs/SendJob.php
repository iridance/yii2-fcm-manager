<?php

namespace fcm\manager\jobs;

use fcm\manager\components\Connection as FCM;

class SendJob extends BaseObject implements \yii\queue\JobInterface
{
    public $notification;

    protected $targetMethods = [
        'topic' => 'sendToTopic',
        'token' => 'sendToTokens',
    ];

    public function execute($queue)
    {
        $fcm = new FCM();

        $target = $this->notification->target;

        $action = $this->targetMethods[$target['type']];

        $result = $fcm->${$action}($target['value'], $this->notification->title, $this->notification->body);

        return $result;
    }
}