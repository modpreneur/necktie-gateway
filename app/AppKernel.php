<?php

namespace Necktie\Gateway;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Necktie\Bundle\GatewayBundle\GatewayBundle;
use Sensio\Bundle\DistributionBundle\SensioDistributionBundle;
use Symfony\Bundle\DebugBundle\DebugBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Trinity\Bundle\BunnyBundle\TrinityBunnyBundle;

/**
 * Class AppKernel
 * @package Necktie\Gateway
 */
class AppKernel extends Kernel
{

    public function registerBundles()
    {
        $bundles = [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new TwigBundle(),
            new GatewayBundle(),
            new TrinityBunnyBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new DebugBundle();
            $bundles[] = new WebProfilerBundle();
            $bundles[] = new SensioDistributionBundle();
        }

        return $bundles;
    }


    public function getRootDir()
    {
        return __DIR__;
    }


    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->environment;
    }


    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }


    /**
     * Loads the container configuration.
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $config = 'config';
        if($this->getEnvironment() !== 'prod'){
            $config .=  $this->getEnvironment();
        }

        $loader->load($this->getRootDir().'/config/'. $config .'.yml');
    }
}