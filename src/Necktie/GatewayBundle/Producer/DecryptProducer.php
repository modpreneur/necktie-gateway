<?php
/**
 * Created by PhpStorm.
 * User: polki
 * Date: 20.1.17
 * Time: 11:21
 */

namespace Necktie\GatewayBundle\Producer;

use Trinity\Bundle\BunnyBundle\AbstractProducer;
use Trinity\Bundle\BunnyBundle\Annotation\Producer;

/**
 * @Producer(
 *     exchange="decrypt_exchange",
 *     contentType="application/json"
 * )
 */

class DecryptProducer extends AbstractProducer
{

}