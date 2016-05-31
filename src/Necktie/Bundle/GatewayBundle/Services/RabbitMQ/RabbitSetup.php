<?php


namespace Necktie\Bundle\GatewayBundle\Services\RabbitMQ;


use Bunny\Client;
use Trinity\Bundle\BunnyBundle\Setup\BaseRabbitSetup;

class RabbitSetup extends BaseRabbitSetup
{

    protected $outputChanel;


    /**
     * RabbitSetup constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        parent::__construct($client, [], 'necktie_exchange');


    }


}