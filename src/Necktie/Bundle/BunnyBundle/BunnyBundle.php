<?php

namespace Necktie\Bundle\BunnyBundle;

use Necktie\Bundle\BunnyBundle\DependencyInjection\BunnyBundleExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;


/**
 * Class BunnyBundle
 * @package Necktie\Bundle\BunnyBundle
 */
class BunnyBundle extends Bundle
{

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }


    /**
     * @return BunnyBundleExtension
     */
    public function getContainerExtension()
    {
        return new BunnyBundleExtension();
    }

}
