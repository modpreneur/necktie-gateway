<?php
namespace Necktie\Bundle\GatewayBundle\Gateway\RequestProcessor;

use Necktie\Bundle\GatewayBundle\Logger\Logger;
use Necktie\Bundle\GatewayBundle\Proxy\ProducerProxy;

/**
 * Class BaseFilter
 * @package Necktie\Bundle\GatewayBundle\Gateway\Filter
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