<?php

namespace Necktie\Bundle\GatewayBundle\Producer;

use Trinity\Bundle\BunnyBundle\AbstractProducer;
use Trinity\Bundle\BunnyBundle\Annotation\Producer;

/**
 * @Producer(
 *     exchange="necktie_exchange",
 *     contentType="application/json"
 * )
 */
class NecktieProducer extends AbstractProducer
{


}