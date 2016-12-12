<?php
/**
 * Created by PhpStorm.
 * User: pocitacapple
 * Date: 12/12/16
 * Time: 14:22
 */

namespace Necktie\GatewayBundle\Producer;

use Trinity\Bundle\BunnyBundle\AbstractProducer;
use Trinity\Bundle\BunnyBundle\Annotation\Producer;

/**
 * @Producer(
 *     exchange="payment_redirect_exchange",
 *     contentType="application/json"
 * )
 */
class PaymentRedirectProducer extends AbstractProducer
{

}