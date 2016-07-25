<?php

namespace Necktie\GatewayBundle;

use Necktie\GatewayBundle\DependencyInjection\NecktieGatewayExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class GatewayBundle
 * @package Necktie\GatewayBundle
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
