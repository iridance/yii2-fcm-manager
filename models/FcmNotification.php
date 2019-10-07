<?php

namespace fcm\manager\models;

use Yii;
use yii\behaviors\AttributesBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * This is the model class for table "fcm_notification".
 *
 * @property int $id
 * @property string $title Notification Title
 * @property string $body Notification body
 * @property string $target Send target
 * @property int $status Status
 * @property int $delay_time Delay to send (seconds)
 * @property string $extra_data Extend data
 * @property string $create_datetime Created datetime
 * @property string $update_datetime Updated datetime
 */
class FcmNotification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fcm_notification';
    }

    public function behaviors()
    {
        return [
            [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_datetime', 'update_datetime'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['update_datetime'],
                ],
                'value'      => new Expression('NOW()'),
            ],
            [
                'class'      => AttributesBehavior::className(),
                'attributes' => [
                    'target' => [
                        ActiveRecord::EVENT_AFTER_FIND    => function () {
                            return Json::decode($this->target);
                        },
                        ActiveRecord::EVENT_BEFORE_INSERT => function () {
                            return Json::encode($this->target);
                        },
                        ActiveRecord::EVENT_AFTER_INSERT  => function () {
                            return Json::decode($this->target);
                        },
                        ActiveRecord::EVENT_BEFORE_UPDATE => function () {
                            return Json::encode($this->target);
                        },
                        ActiveRecord::EVENT_AFTER_UPDATE  => function () {
                            return Json::decode($this->target);
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'body', 'target'], 'required'],
            [['extra_data'], 'string'],
            [['status', 'delay_time'], 'default', 'value' => null],
            [['status', 'delay_time'], 'integer'],
            [['target', 'create_datetime', 'update_datetime'], 'safe'],
            [['title', 'body'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'              => Yii::t('app', 'ID'),
            'title'           => Yii::t('app', 'Title'),
            'body'            => Yii::t('app', 'Body'),
            'target'          => Yii::t('app', 'Target'),
            'status'          => Yii::t('app', 'Status'),
            'delay_time'      => Yii::t('app', 'Delay Time'),
            'extra_data'      => Yii::t('app', 'Extra Data'),
            'create_datetime' => Yii::t('app', 'Create Datetime'),
            'update_datetime' => Yii::t('app', 'Update Datetime'),
        ];
    }

    /**
     * Fcm Notification logs relation.
     *
     * @return ActiveQuery
     */
    public function getNotificationLogs(): ActiveQuery
    {
        return $this->hasMany(FcmNotificationLog::className(), ['fcm_notification_id' => 'id']);
    }

}
