<?php

namespace fcm\manager\components;

use yii\base\Component;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class Connection extends Component
{
    protected $configPath;
    /**
     * Set fcm config file path.
     *
     * @param string $value
     * @return self
     */
    public function setConfigPath(string $value): self
    {
        $this->configPath = $value;
        return $this;
    }

    protected $client;
    /**
     * Set fcm client
     *
     * @param [type] $value
     * @return self
     */
    public function setClient($value): self
    {
        $this->client = $value;
        return $this;
    }

    /**
     * Get fcm client
     *
     * @return Kreait\Firebase\Messaging
     */
    public function getClient(): \Kreait\Firebase\Messaging
    {
        if ($this->configPath === null) {
            throw new \yii\base\InvalidConfigException("Property 'configPath' cannot be null.");
        }

        if ($this->client === null) {
            $firebase = (new Factory)
                ->withServiceAccount($this->configPath)
                ->create();
    
            $this->client = $firebase->getMessaging();
        }

        return $this->client;
    }

    /**
     * Subscribe device in global topic.
     *
     * @param string|array $tokens
     * @return array
     */
    public function subscribe($tokens): array
    {
        return $this->subscribeToTopic('all', $tokens);
    }
    
    /**
     * Subscribe to topic
     *
     * @param string $topic
     * @param string|array $tokens
     * @return array
     */
    public function subscribeToTopic(string $topic, $tokens): array
    {
        if (is_string($tokens)) {
            $tokens = [$tokens];
        }
        
        if (count($tokens) > 1000) {
            throw new \yii\base\InvalidArgumentException('The number of tokens cannot exceed 1000.');
        }

        return $this->getClient()->subscribeToTopic($topic, $tokens);
    }

    /**
     * Unsubscribe from topic
     *
     * @param string $topic
     * @param string|array $tokens
     * @return array
     */
    public function unsubscribeFromTopic(string $topic, $tokens): array
    {
        if (is_string($tokens)) {
            $tokens = [$tokens];
        }
        
        if (count($tokens) > 1000) {
            throw new \yii\base\InvalidArgumentException('The number of tokens cannot exceed 1000.');
        }

        return $this->getClient()->unsubscribeFromTopic($topic, $tokens);
    }

    /**
     * Unsubscribe all topic by device
     *
     * @param string $token
     * @return array
     */
    public function flushTopicByDevice($token): array
    {
        $results = [];
        $instance = $this->getClient()->getAppInstance($token);
        $subscriptions = $instance->topicSubscriptions();

        foreach ($subscriptions as $subscription) {
            $results[] = $this->unsubscribeFromTopic($subscription->topic(), $token);
        }

        return $results;
    }

    /**
     * Send message to topic
     *
     * @param string $topic
     * @param string $title
     * @param string $body
     * @param string $image
     * @return array
     */
    public function sendToTopic(string $topic, string $title, string $body, string $image = null): array
    {
        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification(Notification::create($title, $body, $image));

        return $this->getClient()->send($message);
    }

    /**
     * Send message to device
     *
     * @param string|array $tokens
     * @param array $notification
     * @return array
     */
    public function sendToDevice($tokens, string $title, string $body, string $image = null): array
    {
        if (is_string($tokens)) {
            $tokens = [$tokens];
        }

        if (count($tokens) > 100) {
            throw new \yii\base\InvalidArgumentException('The number of tokens cannot exceed 100.');
        }

        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body, $image));

        $sendReport = $this->getClient()->sendMulticast($message, $tokens);

        $items = $sendReport->getItems();

        $result = [];
        foreach ($items as $item) {
            $result[] = $item->result();
        }

        return $result;
    }

    /**
     * Send message to all device
     * 
     * ***Must subscribe device to 'all' topic.***
     *
     * @param string $title
     * @param string $body
     * @param string $image
     * @return array
     */
    public function sendToAllDevice(string $title, string $body, string $image = null): array
    {
        $message = CloudMessage::withTarget('topic', 'all')
            ->withNotification(Notification::create($title, $body, $image));

        return $this->getClient()->send($message);
    }

    /**
     * Get device info by token.
     *
     * @param array|string $deviceTokens
     * @return array
     */
    public function getDeviceInfo($deviceTokens): array
    {
        if (is_string($deviceTokens)) {
            $deviceTokens = [$deviceTokens];
        }

        $result = [];

        foreach ($deviceTokens as $token) {
            $instance = $this->getClient()->getAppInstance($token);
            $result[$token] = $instance->rawData();
        }
        return $result;
    }

    /**
     * Device register instance.
     *
     * @var \fcm\manager\models\DeviceRegisterInterface|null
     */
    protected $deviceRegisterClass;

    /**
     * Device register class setter method.
     *
     * @param string $className
     * @return self
     */
    public function setDeviceRegisterClass(string $className): self
    {
        $instance = new $className();
        if ($instance instanceof \fcm\manager\models\DeviceRegisterInterface === false) {
            throw new \yii\base\InvalidConfigException('deviceRegisterClass must be implemented by \fcm\manager\models\DeviceRegisterInterface.');
        }
        $this->deviceRegisterClass = $instance;
        return $this;
    }

    /**
     * Device register class getter method.
     *
     * @return void
     */
    public function getDeviceRegisterClass(): \fcm\manager\models\DeviceRegisterInterface
    {
        if ($this->deviceRegisterClass === null) {
            $this->deviceRegisterClass = new \fcm\manager\models\FcmDeviceRegister();
        }
        return $this->deviceRegisterClass;
    }

}