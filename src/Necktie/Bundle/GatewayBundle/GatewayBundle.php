<?php

namespace Necktie\Bundle\GatewayBundle;

use Necktie\Bundle\GatewayBundle\DependencyInjection\NecktieGatewayExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 * Class GatewayBundle
 * @package Necktie\Bundle\GatewayBundle
 */
class GatewayBundle extends Bundle
{

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

    }


    /**
     * @return NecktieGatewayExtension
     */
    public function getContainerExtension()
    {
        return new NecktieGatewayExtension();
    }
}
