<?php


namespace Necktie\GatewayBundle\Logger;


use Doctrine\ORM\EntityManager;
use Necktie\GatewayBundle\Entity\SystemLog;


/**
 * Class Logger
 * @package Necktie\GatewayBundle\Logger
 */
class Logger
{

    /** @var  EntityManager */
    protected $manager;


    /**
     * Logger constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager = null)
    {
        $this->manager = $manager;
    }


    /**
     * @param EntityManager $manager
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
    }


    /**
     * @param array $data
     * @param int $level
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function addRecord($data, $level = 200)
    {
        $em = $this->manager;

        $sys = new SystemLog();
        $sys->setCreatedAt(new \DateTime());

        $sys->setLevel($level);
        $sys->setLog(json_encode($data));
        $sys->setUrl($data['url']);

        $em->persist($sys);
        $em->flush($sys);
    }

}