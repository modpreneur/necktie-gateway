<?php
namespace Necktie\GatewayBundle\Gateway\RequestProcessor;

use Necktie\GatewayBundle\Logger\Logger;
use Necktie\GatewayBundle\Proxy\ProducerProxy;

/**
 * Class BaseFilter
 * @package Necktie\GatewayBundle\Gateway\Filter
 */
abstract class BaseProcessor
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * BaseFilter constructor.
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }


    public function getName() : string
    {
        $ref = new \ReflectionClass($this);
        return $ref->getShortName();
    }


    abstract public function process(array $message = []);
}