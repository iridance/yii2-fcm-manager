# FCM Manager for Yii2

## Installation

### Composer Install

```
composer require irice/yii2-fcm-manager
```
## Database Migration
### cofnig/console.php
```php
return [
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => null,
            'migrationNamespaces' => [
                'fcm\manager\migrations',
                ...
            ],
        ],
    ],
    ...
];
```
And run migrate command
```
php yii migrate
```

## Configuration
### config/web.php
```php
return [
    'modules' => [
        'fcm' => [
            'class' => 'fcm\manager\Module',
            ...
        ],
        ...
    ],
    ...
    'components' => [
        'fcm' => [
            'class' => 'fcm\manager\components\Connection',
            // you can download config file on firebase console 'https://console.firebase.google.com/u/1/project/<your_project>/settings/serviceaccounts/adminsdk'
            'configPath' => __DIR__ . '/<your_project>-firebase-adminsdk.json',
        ],
        ...
    ],
    ...
];

```
## Basic Usage

### Register device to database
```php
$result = Yii::$app->fcm->deviceRegisterClass::registerDevice(
    '<registed_device_token>', // or ['<registed_device_token1>', '<registed_device_token2>']
    <user_id> // user identity id
);
```

### Subscribe device to Topic
```php
$result = Yii::$app->fcm->subscribeToTopic(
    '<topic-name>',
    '<registed_device_token>' // or ['<registed_device_token1>', '<registed_device_token2>']
);
```

### Unsubscribe device from Topic
```php
$result = Yii::$app->fcm->unSubscribeFromTopic(
    '<topic-name>',
    '<registed_device_token>' // or ['<registed_device_token1>', '<registed_device_token2>']
);
```

### Send message to Topic
```php
$result = Yii::$app->fcm->sendToTopic(
    '<topic-name>',
    '<message title>',
    '<message content>',
    '<message image url>' // optional
);
```

### Send message to Device
```php
$result = Yii::$app->fcm->sendToTokens(
    '<registed_device_token>' // or ['<registed_device_token1>', '<registed_device_token2>']
    '<message title>',
    '<message content>',
    '<message image url>' // optional
);
```

## Schedule Send Message
The feature implement by Yii2-queue extension.
### config/console.php (config/web.php)
```php
'components' => [
    'queue' => [
        'class' => 'yii\queue\db\Queue',
        'serializer' => 'fcm\manager\components\PhpSerializer',
        'deleteReleased' => false,
        'as log' => 'yii\queue\LogBehavior',
    ],
    'mutex' => [
        'class' => 'yii\mutex\PgsqlMutex',
    ],
    'fcm' => [
        'class' => 'fcm\manager\components\Connection',
        'configPath' => __DIR__ . '/<your_project>-firebase-adminsdk.json',
    ],
    ...
]
```

### Schedule a message to Topic
```php
$job = new \fcm\manager\jobs\SendJob([
    'notification' => [
        'title' => '<message-title>',
        'body' => '<message-body>',
        'target' => [
            'type' => \fcm\manager\jobs\SendJob::TYPE_TOPIC,
            'value' => '<topic-name>',
        ],
    ],
]);

$delayTime = strtotime('2019-12-31 00:00:00') - time(); //Send message on specified time.
//$delayTime = strtotime('next Saturday') - time(); //Send message on next weekend.
//$delayTime = 60 * 60 * 24; //Send message after 24 hours.

$queueId = Yii::$app->queue->delay($delayTime)->push($job);
```
#### Auto update fcm progress after send
Implement NotificationInterface on custom class
```php
class Notification extends \yii\db\ActiveRecord implements \fcm\manager\models\NotificationInterface
{
    ... 
    public function getSuccessStatus()
    {
        return static::STATUS['SENDED'];
    }

    public function getFailStatus()
    {
        return static::STATUS['FAILED'];
    }

    public function updateStatus($value)
    {
        $this->status = $value;
        return $this->save();
    }
}
```

## TODO

### Notification GUI Manager

## Requirements
Yii2-queue
