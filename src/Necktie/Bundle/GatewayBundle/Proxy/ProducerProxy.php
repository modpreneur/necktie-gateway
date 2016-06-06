<?php


namespace Necktie\Bundle\GatewayBundle\Proxy;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;


/**
 * Class ProducerProxy
 * @package Necktie\Bundle\GatewayBundle\Proxy
 */
class ProducerProxy implements ProducerInterface
{

    /** @var  KernelInterface */
    protected $kernel;


    /**
     * ProducerProxy constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }


    /**
     * @param string $data
     * @param string $queueName
     * @return string
     */
    public function publish(string $data, string $queueName = '')
    {
        $kernel = $this->kernel;
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $command = 'bunny:producer ';

        $input = $queueName ? $command.$queueName.' '.'\''.$data.'\'' : $command.$data;

        $input = new StringInput($input);
        $output = new BufferedOutput();

        // error
        $application->run($input, $output);

        // todo - log outout !!!
        return $output->fetch();
    }
}