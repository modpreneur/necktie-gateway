<?php
namespace Tests;


use  Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


/**
 * Class TestCase
 * @package Necktie\Bundle\GatewayBundle\Gateway
 */
class TestCase extends WebTestCase
{


    protected static function getKernelClass()
    {
        require_once __DIR__.'/../app/AppKernel.php';
        return '\\Necktie\\Gateway\\AppKernel';
    }


    /**
     * @param string $serviceName
     * @return object
     */
    protected function get($serviceName)
    {
        $kernel = $this->createClient()->getKernel();
        $container = $kernel->getContainer();
        return $container->get($serviceName);
    }

}