<?php

namespace fcm\manager\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "fcm_device_register".
 *
 * @property int $id
 * @property int $user_id Relation to user table
 * @property string $token device token
 * @property string $platform token platform
 * @property string $create_datetime Created datetime
 * @property string $last_use_time Last use datetime
 */
class FcmDeviceRegister extends \yii\db\ActiveRecord implements DeviceRegisterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fcm_device_register';
    }

    public function behaviors()
    {
        return [
            [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_datetime', 'last_use_time'],
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
            [['user_id', 'token'], 'required'],
            [['user_id'], 'default', 'value' => null],
            [['user_id'], 'integer'],
            [['create_datetime', 'last_use_time'], 'safe'],
            [['token', 'platform'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'              => Yii::t('app', 'ID'),
            'user_id'         => Yii::t('app', 'User ID'),
            'token'           => Yii::t('app', 'Token'),
            'platform'        => Yii::t('app', 'Platform'),
            'create_datetime' => Yii::t('app', 'Create Datetime'),
            'last_use_time'   => Yii::t('app', 'Last Use Time'),
        ];
    }

    /**
     * Implements interface, save device info to database.
     *
     * @param array|string $deviceTokens
     * @param int $user_id
     * @return boolean
     */
    public static function registerDevice($deviceTokens, int $user_id): bool
    {
        $results = [];
        $instance = Yii::$app->fcm->getDeviceInfo($deviceTokens);

        foreach ($instance as $token => $info) {
            $query = static::find()->andWhere(['token' => $token]);
            if (!$query->exists()) {
                $subscribeGlobal = Yii::$app->fcm->subscribe($token);
                $model = new static([
                    'user_id'  => $user_id,
                    'token'    => $token,
                    'platform' => $info['platform'], //. ':' . $info['application'] //optionals info
                ]);
                $results[] = $model->save();
                continue;
            }

            $model = $query->one();
            if ($user_id != $model->user_id) { //device change user.
                $results[] = Yii::$app->fcm->flushTopicByDevice($token);
                $subscribeGlobal = Yii::$app->fcm->subscribe($token);
                $model->user_id = $user_id;
            }
            $model->last_use_time = date('Y-m-d H:i:s');
            $model->save();
        }

        return count($results) > 0 ? true : false;
    }

    /**
     * Implements interface, remove device info from database.
     *
     * @param array|string $deviceTokens
     * @return boolean
     */
    public static function unRegisterDevice($deviceTokens): bool
    {
        if (is_string($deviceTokens)) {
            $deviceTokens = [$deviceTokens];
        }

        $results = [];

        foreach ($deviceTokens as $device) {
            $results[] = Yii::$app->fcm->flushTopicByDevice($device);
            $deleteResult = static::deleteAll(['token' => $device]);
        }

        return count($results) > 0 ? true : false;
    }
}
