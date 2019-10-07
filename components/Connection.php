<?php

namespace fcm\manager\components;

use yii\base\Component;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class Connection extends Component
{
    protected $_client;

    /**
     * Set fcm client
     *
     * @param [type] $value
     * @return Connection
     */
    public function setClient($value) : self
    {
        $this->_client = $value;
        return $this;
    }

    /**
     * Get fcm client
     *
     * @return Kreait\Firebase\Messaging
     */
    public function getClient() : \Kreait\Firebase\Messaging
    {
        if ($this->_client === null) {
            $path = Yii::$app->params['firebase']['config-path'];

            $firebase = (new Factory)
                ->withServiceAccount($path)
                ->create();
    
            $this->_client = $firebase->getMessaging();
        }

        return $this->_client;
    }
    
    /**
     * Subscribe to topic
     *
     * @param string $topic
     * @param mixed $tokens
     * @return boolean
     */
    public function subscribeToTopic(string $topic, mixed $tokens) 
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
     * @param mixed $tokens
     * @return boolean
     */
    public function unsubscribeFromTopic(string $topic, mixed $tokens) 
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
     * Send message to topic
     *
     * @param string $topic
     * @param string $title
     * @param string $body
     * @param string $image
     * @return boolean
     */
    public function sendToTopic(string $topic, string $title, string $body, string $image = null) 
    {
        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification(Notification::create($title, $body, $image));

        return $this->getClient()->send($message);
    }

    /**
     * Send message to tokens
     *
     * @param mixed $tokens
     * @param array $notification
     * @return boolean
     */
    public function sendToTokens(mixed $tokens, string $title, string $body, string $image = null) 
    {
        if (is_string($tokens)) {
            $tokens = [$tokens];
        }

        if (count($tokens) > 100) {
            throw new \yii\base\InvalidArgumentException('The number of tokens cannot exceed 100.');
        }

        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body, $image));

        return $this->getClient()->sendMulticast($message, $tokens);
    }

    /**
     * Send message to all device
     * 
     * ***Must subscribe device to 'all' topic.***
     *
     * @param string $title
     * @param string $body
     * @param string $image
     * @return boolean
     */
    public function sendToAllDevice(string $title, string $body, string $image = null) 
    {
        $message = CloudMessage::withTarget('topic', 'all')
            ->withNotification(Notification::create($title, $body, $image));

        return $this->getClient()->send($message);
    }

}