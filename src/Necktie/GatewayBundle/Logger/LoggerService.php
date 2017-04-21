<?php

namespace Necktie\GatewayBundle\Logger;

use Doctrine\ORM\EntityManager;
use Necktie\GatewayBundle\Logger\Entity\Logger;
use Trinity\Bundle\LoggerBundle\Services\ElasticLogService;

/**
 * Class Logger
 * @package Necktie\GatewayBundle\Logger
 */
class LoggerService
{

    /** @var  ElasticLogService */
    protected $logger;


    /**
     * Logger constructor.
     * @param ElasticLogService $elasticLog
     */
    public function __construct(ElasticLogService $elasticLog = null)
    {
        $this->logger = $elasticLog;
    }


    /**
     * @param EntityManager $logger
     */
    public function setManager($logger)
    {
        $this->logger = $logger;
    }


    /**
     * @param array $data
     * @param int $level
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function addRecord($data, $level = 200)
    {
        $logger = $this->logger;

        $sys = new Logger();
        $sys->setLevel($level);
        $sys->setUrl($data['url']);
        $sys->setLog(json_encode($data));

        $logger->writeInto('Logger', $sys);
    }
}
