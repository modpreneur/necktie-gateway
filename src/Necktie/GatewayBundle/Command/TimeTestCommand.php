<?php

namespace Necktie\GatewayBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Necktie\AppBundle\Entity\Client;

/**
 * Class TimeTestCommand
 * @package Necktie\AppBundle\Command
 */
class TimeTestCommand extends ContainerAwareCommand
{
    private $shouldStop = false;

    protected function configure()
    {
        $this
            ->setName('necktie:test')
            ->setDescription('Test kill command');
    }


    /**
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \LogicException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        pcntl_signal(SIGTERM, [$this, 'stopCommand']);
        pcntl_signal(SIGINT, [$this, 'stopCommand']);

        $this->shouldStop = false;

        $output->writeln("<info>Ahoj</info>");
        pcntl_signal_dispatch();

        for ($i = 0; $i < 10; $i++) {
            if ($this->shouldStop) return;
            sleep($i);
            $output->writeln($i);
        }

        $output->writeln("<info>Ahoj</info>");
    }


    public function stopCommand()
    {
    }
}
