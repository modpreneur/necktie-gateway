<?php

namespace Necktie\Bundle\GatewayBundle;

use Necktie\AppBundle\DependencyInjection\FilterCompilerPass;
use Necktie\Bundle\GatewayBundle\DependencyInjection\NecktieGatewayExtension;
use OldSound\RabbitMqBundle\DependencyInjection\Compiler\RegisterPartsPass;
use OldSound\RabbitMqBundle\DependencyInjection\OldSoundRabbitMqExtension;
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
