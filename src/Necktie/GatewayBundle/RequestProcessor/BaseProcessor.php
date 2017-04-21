<?php
namespace Necktie\GatewayBundle\Gateway\RequestProcessor;

use Necktie\GatewayBundle\Logger\LoggerService;

/**
 * Class BaseFilter
 * @package Necktie\GatewayBundle\Gateway\Filter
 */
abstract class BaseProcessor
{
    /**
     * @var LoggerService
     */
    protected $logger;

    /**
     * BaseFilter constructor.
     * @param LoggerService $logger
     */
    public function __construct(LoggerService $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @return string
     */
    public function getName() : string
    {
        $ref = new \ReflectionClass($this);
        return $ref->getShortName();
    }


    /**
     * @param array $message
     *
     * @return mixed
     */
    abstract public function process(array $message = []);
}