<?php

namespace Necktie\Gateway;

use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        );

        return $bundles;
    }


    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }


    protected function configureRoutes(RouteCollectionBuilder $routes) {}

}