<?php

namespace Necktie\Bundle\GatewayBundle\Command;

use Bunny\Message;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trinity\Bundle\BunnyBundle\Command\AbstractConsumerCommand;

/**
 * Class NecktieConsumerCommand
 * @package Necktie\Bundle\GatewayBundle\Command
 */
class NecktieConsumerCommand extends AbstractConsumerCommand
{

    protected function configure(){
        parent::configure();

        $this
            ->setName('necktie:rabbit:consumer')
            ->setDescription('Rabbit consumer.');
    }


    /**
     * @param Message $msg
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    public function consume(Message $msg, InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $p = $this->getContainer()->get('necktie.listener.message_processor');
        $p->setOutput($output);

        $em->beginTransaction();
        $output->writeln('Consumer command');

        try {
            $message = new \Necktie\Bundle\GatewayBundle\Entity\Message();
            $message->setMessage($msg->content);
            $message->setDeliveredAt(new \DateTime());
            $message->setDeliveryTag($msg->deliveryTag);

            $em->persist($message);

            $em->flush($message);
            $em->commit();

            
        } catch (\Exception $ex) {
            $em->rollback();
            $output->writeln('['.(new \DateTime())->format(\DateTime::W3C).'] <error>'.$ex->getMessage().'</error>');
            //@todo - log error
            throw $ex; 
        }
    }
}