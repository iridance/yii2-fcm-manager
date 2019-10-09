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
     * @var \fcm\manager\models\NotificationsInterface|ActiveRecord|array
     */
    protected $notification;

    /**
     * Notification object setter
     *
     * @param \fcm\manager\models\NotificationsInterface|ActiveRecord|array $value
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
     * @param \fcm\manager\models\NotificationsInterface|ActiveRecord|array $value
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

        if ($this->notification instanceof \fcm\manager\models\NotificationsInterface) {
            $status = isset($result['name']) ? 'successStatus' : 'failStatus';
            $this->notification->updateStatus($this->notification->${$status});
        }

        //TODO: save api result to log.

        return $result;
    }
}
