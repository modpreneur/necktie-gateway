<?php

namespace Necktie\GatewayBundle\Logger\Entity;

use Trinity\Bundle\LoggerBundle\Entity\BaseElasticLog;

/**
 * Class Message
 * @package Necktie\GatewayBundle\Logger\Entity
 */
class Message extends BaseElasticLog
{
    /**
     * @var string
     */
    protected $message;


    /** @var  string */
    protected $tag;


    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }


    /**
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }


    /**
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }


    /**
     * @param string $tag
     */
    public function setTag(string $tag)
    {
        $this->tag = $tag;
    }
}
