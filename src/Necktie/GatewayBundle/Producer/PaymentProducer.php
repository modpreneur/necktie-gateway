<?php

namespace Necktie\GatewayBundle\Producer;

use Trinity\Bundle\BunnyBundle\AbstractProducer;
use Trinity\Bundle\BunnyBundle\Annotation\Producer;

/**
 * @Producer(
 *     exchange="payment_exchange",
 *     contentType="application/json"
 * )
 */
class PaymentProducer extends AbstractProducer
{


}