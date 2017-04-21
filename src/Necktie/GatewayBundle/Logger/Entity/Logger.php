<?php

namespace Necktie\GatewayBundle\Logger\Entity;

use Trinity\Bundle\LoggerBundle\Entity\BaseElasticLog;

/**
 * Class Logger
 * @package Necktie\GatewayBundle\Logger\Entity
 */
class Logger extends BaseElasticLog
{

    /**
     * @var string
     */
    protected $log;

    /** @var  string */
    protected $level;

    /** @var  string */
    protected $url;


    /**
     * @return string
     */
    public function getLog(): string
    {
        return $this->log;
    }


    /**
     * @param string $log
     */
    public function setLog(string $log)
    {
        $this->log = $log;
    }


    /**
     * @return string
     */
    public function getLevel(): string
    {
        return $this->level;
    }


    /**
     * @param string $level
     */
    public function setLevel(string $level)
    {
        $this->level = $level;
    }


    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }


    /**
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }
}
