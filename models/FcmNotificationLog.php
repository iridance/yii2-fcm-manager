<?php

namespace fcm\manager\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "fcm_notification_log".
 *
 * @property int $id
 * @property int $fcm_notification_id Relation to fcm_notifications
 * @property string $result Firebase response
 * @property int $status Send status
 * @property string $send_datetime Send datetime
 */
class FcmNotificationLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fcm_notification_log';
    }

    public function behaviors()
    {
        return [
            [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['send_datetime'],
                ],
                'value'      => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fcm_notification_id', 'result', 'send_datetime'], 'required'],
            [['fcm_notification_id', 'status'], 'default', 'value' => null],
            [['fcm_notification_id', 'status'], 'integer'],
            [['result'], 'string'],
            [['send_datetime'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                  => Yii::t('app', 'ID'),
            'fcm_notification_id' => Yii::t('app', 'Fcm Notification ID'),
            'result'              => Yii::t('app', 'Result'),
            'status'              => Yii::t('app', 'Status'),
            'send_datetime'       => Yii::t('app', 'Send Datetime'),
        ];
    }
}
