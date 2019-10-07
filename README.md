# FCM Manager for Yii2

## Installation

### Composer Install

```
composer require irice/yii2-fcm-manager
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
        ],
        ...
    ],
    ...
];

```

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

## TODO

### Notification GUI Manager

### Schedule Message

## Requirements

