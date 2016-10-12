<?php

namespace Necktie\GatewayBundle\Gateway\Tests;

use Necktie\Gateway\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class IndexTest
 * @package Necktie\GatewayBundle\Gateway\Tests
 */
class IndexTest extends WebTestCase
{
    protected static function getKernelClass()
    {
        return AppKernel::class;
    }


    public function testIndex()
    {
       $client = self::createClient();
       $client->request('GET', '/');
       $content = $client->getResponse()->getContent();
       self::assertContains('RUNNING', $content);
       self::assertContains('gatewayConsumer', $content);
       self::assertJson($content);
    }
}