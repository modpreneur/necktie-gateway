<?php

namespace Necktie\Bundle\GatewayBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class NecktieConsumerCommand
 * @package Necktie\Bundle\GatewayBundle\Command
 */
class NecktieConsumerCommand extends ContainerAwareCommand
{

    protected $queueName = 'queue_gateway';

    protected $maxMessages = 10;


    protected function configure()
    {
        parent::configure();

        $this->setName('gateway:necktie:consume')
            ->setDescription('Rabbit consumer.');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        echo 'Start consuming.'.PHP_EOL;
        $consumer = $this->getContainer()->get('gateway.consumer.command_consumer');

        $consumer->startConsuming($this->queueName, $this->maxMessages);
    }


}