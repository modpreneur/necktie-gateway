<?php


namespace Necktie\GatewayBundle\Event;


use Symfony\Component\EventDispatcher\Event;


class MessageEvent extends Event
{

    const EVENT = 'message.consume';

    /**
     * @var string
     */
    protected $content;

    protected $deliveryTag;


    /**
     * @var string
     */
    private $queue;


    public function __construct(string $content, string $queue, $deliveryTag)
    {
        $this->content = $content;
        $this->queue = $queue;
        $this->deliveryTag = $deliveryTag;
    }


    /**
     * @return object|mixed
     */
    public function getContent()
    {
        return unserialize($this->content);
    }


    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        if (is_string($content)) {
            $this->content = $content;
        } else {
            $this->content = serialize($content);
        }

        return $this;
    }


    /**
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }


    /**
     * @return mixed
     */
    public function getDeliveryTag()
    {
        return $this->deliveryTag;
    }


}