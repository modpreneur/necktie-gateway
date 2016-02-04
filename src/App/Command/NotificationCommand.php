<?php

namespace App\Command;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotificationCommand  extends ContainerAwareCommand
{

    protected $count = 0;

    protected function configure(){
        $this
            ->setName('necktie:rabbit:consumer')
            ->setDescription('Start waiting for messages.')
            ->addArgument('consumer', InputArgument::REQUIRED, 'Consumer name.');
    }


    protected function execute(InputInterface $input, OutputInterface $output){

        $consumer = $input->getArgument('consumer');

        $server   = $this->getContainer()->getParameter('rabbit_server');
        $port     = $this->getContainer()->getParameter('rabbit_port');
        $user     = $this->getContainer()->getParameter('rabbit_user');
        $password = $this->getContainer()->getParameter('rabbit_password');

        //$this->getContainer()->get('neckie.gateway.api_gateway')->request('POST', 'http://seznam.cz');

        var_dump($consumer);

        $connection = new AMQPStreamConnection('necktie.docker', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare($consumer, false, true, false, false);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

        $callback = function($msg) {
            $r = $this->process($msg);
            $this->handleProcessMessage($msg, $r);
        };

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume($consumer, '', false, false, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }
    }


    /**
     * @param AMQPMessage $msg
     * @param bool|string $processFlag
     */
    private function handleProcessMessage(AMQPMessage $msg, $processFlag)
    {
        if ($processFlag == false && $this->count > 10){
            $msg->delivery_info['channel']->basic_reject($msg->delivery_info['delivery_tag'], false);
        } elseif($processFlag){
            $msg->delivery_info['channel']->basic_reject($msg->delivery_info['delivery_tag'], true);
            $this->count++;
        } else
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

    }


    /**
     * @param AMQPMessage $msg
     * @return bool
     */
    private function process(AMQPMessage $msg)
    {
        $message = unserialize($msg->body);

        if(isset($message['header'])){
            $header = $message['header'];
        }else{
            $header = [];
        }

        if(isset($message['body'])){
            $body = $message['body'];
        }else{
            $body = null;
        }

        try{
           $request = $this->getContainer()
                ->get('neckie.gateway.api_gateway')
                ->request($message['method'], $message['url'], $header, $body);

        }catch(\Exception $ex){
            var_dump('Error');
            return false;
        }

        return true;
    }

}