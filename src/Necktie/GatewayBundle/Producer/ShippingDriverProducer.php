<?php

namespace Necktie\GatewayBundle\Producer;

use Trinity\Bundle\BunnyBundle\AbstractProducer;
use Trinity\Bundle\BunnyBundle\Annotation\Producer;

/**
 * @Producer(
 *     exchange="shipping_driver_exchange",
 *     contentType="application/json"
 * )
 */
class ShippingDriverProducer extends AbstractProducer
{
}
