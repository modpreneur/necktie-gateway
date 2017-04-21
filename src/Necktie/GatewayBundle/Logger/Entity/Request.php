<?php

namespace Necktie\GatewayBundle\Logger\Entity;

use Trinity\Bundle\LoggerBundle\Entity\BaseElasticLog;

/**
 * Class Message
 * @package Necktie\GatewayBundle\Logger\Entity
 */
class Request extends BaseElasticLog
{

    /** @var  string */
    protected $url;

    /** @var  string */
    protected $response;

    /** @var  string|int */
    protected $status;

    /** @var  array */
    protected $options;

    /** @var  string */
    protected $tag;

    /** @var  string */
    protected $method;


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


    /**
     * @return string
     */
    public function getResponse(): string
    {
        return $this->response;
    }


    /**
     * @param string $response
     */
    public function setResponse(string $response)
    {
        $this->response = $response;
    }


    /**
     * @return int|string
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * @param int|string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }


    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }


    /**
     * @return string
     */
    public function getTag(): ?string
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


    /**
     * @return string
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }


    /**
     * @param string $method
     */
    public function setMethod(string $method)
    {
        $this->method = $method;
    }
}
