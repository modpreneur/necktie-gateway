<?php


namespace Necktie\Bundle\GatewayBundle\Proxy;


interface ProducerInterface
{

    /**
     * @param string $data
     * @param string $queueName
     * @return void
     */
    public function publish(string $data, string $queueName = '');

}