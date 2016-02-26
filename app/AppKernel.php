<?php

namespace Necktie\Gateway;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Necktie\Bundle\BunnyBundle\BunnyBundle;
use Necktie\Bundle\GatewayBundle\GatewayBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * Class AppKernel
 * @package Necktie\Gateway
 */
class AppKernel  extends Kernel
{

    use MicroKernelTrait;

    public function registerBundles()
    {
        $bundles = array(
            new FrameworkBundle(),
            new GatewayBundle(),
            new DoctrineBundle(),
            new BunnyBundle(),
        );


        return $bundles;
    }


    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }


    protected function configureRoutes(RouteCollectionBuilder $routes) {}

}