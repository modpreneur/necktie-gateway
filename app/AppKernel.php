<?php

namespace Necktie\Gateway;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Necktie\Bundle\GatewayBundle\GatewayBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Trinity\Bundle\BunnyBundle\BunnyBundle;


/**
 * Class AppKernel
 * @package Necktie\Gateway
 */
class AppKernel extends Kernel
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
        $loader->load(__DIR__.'/config/config.yml');
    }


    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }


    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }


    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $routes->add('/', 'kernel:indexAction', 'homepage');
    }


    /*
     * Check service?
     *
     */
    public function indexAction()
    {
        return new JsonResponse('Ok');
    }

}