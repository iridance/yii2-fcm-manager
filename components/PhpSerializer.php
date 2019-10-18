<?php

namespace fcm\manager\components;

use yii\base\BaseObject;
use yii\queue\serializers\SerializerInterface;

use function Opis\Closure\serialize;
use function Opis\CLosure\unserialize;

class PhpSerializer extends BaseObject implements SerializerInterface
{
    public function serialize($job)
    {
        return serialize($job);
    }

    public function unserialize($serialized)
    {
        return unserialize($serialized);
    }
}
