<?php

namespace Necktie\Bundle\GatewayBundle;

use Necktie\Bundle\GatewayBundle\DependencyInjection\NecktieGatewayExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 * Class GatewayBundle
 * @package Necktie\Bundle\GatewayBundle
 */
class GatewayBundle extends Bundle
{

    /**
     * @return NecktieGatewayExtension
     */
    public function getContainerExtension()
    {
        return new NecktieGatewayExtension();
    }

}
