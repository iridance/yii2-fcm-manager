<?php

namespace fcm\manager\jobs;

use Yii;
use yii\base\BaseObject;

class SendJob extends BaseObject implements \yii\queue\JobInterface
{
    /**
     * Type of topic for notification target
     */
    const TYPE_TOPIC = 'topic';

    /**
     * Type of device for notification target
     */
    const TYPE_DEVICE = 'device';

    /**
     * Type of all device for notification target
     */
    const TYPE_ALL = 'all';

    /**
     * Notification object
     *
     * @var \fcm\manager\models\NotificationsInterface|mixed
     */
    protected $notification;

    /**
     * Notification object setter
     *
     * @param \fcm\manager\models\NotificationsInterface|mixed $value
     * @return self
     */
    public function setNotification($value): self
    {
        $this->validateNotification($value);

        $this->notification = $value;
        return $this;
    }

    /**
     * Notification format validation.
     *
     * @param \fcm\manager\models\NotificationsInterface|mixed $value
     * @return void
     */
    protected function validateNotification($value)
    {
        $rules = ['title', 'body', 'target'];

        foreach ($rules as $rule) {
            if (!isset($value[$rule])) {
                throw new \yii\base\InvalidParamException("Notification must have '$rule'.");
            }
        }
    }

    /**
     * Fcm component send method list.
     *
     * @var array
     */
    protected $targetMethods = [
        'topic'  => 'sendToTopic',
        'device' => 'sendToDevice',
        'all'    => 'sendToAllDevice',
    ];

    public function execute($queue)
    {
        $fcm = Yii::$app->fcm;

        $target = $this->notification['target'];
        $action = $this->targetMethods[$target['type']];
        $result = $fcm->$action($target['value'], $this->notification['title'], $this->notification['body']);
        
        try {
            $status = isset($result['name']) ? 'successStatus' : 'failStatus';
            static::saveStatus($this->notification, $status);
        } catch (\TypeError $e) {
            Yii::info($e->getMessage(), 'fcm:queue');
        }

        //TODO: save api result to log.

        return $result;
    }

    /**
     * save status to owner notification model.
     *
     * @param \fcm\manager\models\NotificationInterface $instance
     * @param mixed $status
     * @return void
     */
    public static function saveStatus(\fcm\manager\models\NotificationInterface $instance, $status)
    {
        return $instance->updateStatus($instance->$status);
    }
}
