<?php

namespace Necktie\Gateway;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use FOS\UserBundle\FOSUserBundle;
use HWI\Bundle\OAuthBundle\HWIOAuthBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Necktie\GatewayBundle\GatewayBundle;
use Sensio\Bundle\DistributionBundle\SensioDistributionBundle;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Symfony\Bundle\DebugBundle\DebugBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Trinity\Bundle\BunnyBundle\TrinityBunnyBundle;
use Trinity\Bundle\LoggerBundle\LoggerBundle;

/**
 * Class AppKernel
 * @package Necktie\Gateway
 */
class AppKernel extends Kernel
{
    /**
     * @return array
     */
    public function registerBundles()
    {
        $bundles = [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new TwigBundle(),
            new GatewayBundle(),
            new TrinityBunnyBundle(),
            new SensioFrameworkExtraBundle(),
            new SecurityBundle(),
            new HWIOAuthBundle(),
            new LoggerBundle(),
            new JMSSerializerBundle(),
            new MonologBundle(),
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
        return dirname(__DIR__).'/var/logs/'.$this->environment;
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
            $config .= '_' . $this->getEnvironment();
        }

        $loader->load($this->getRootDir().'/config/'. $config .'.yml');
    }
}