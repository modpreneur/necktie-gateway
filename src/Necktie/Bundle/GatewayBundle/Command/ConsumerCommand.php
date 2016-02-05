<?php

namespace Necktie\Bundle\GatewayBundle\Command;

use Necktie\Bundle\GatewayBundle\Entity\SystemLog;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ConsumerCommand extends ContainerAwareCommand
{

    protected $count = 0;

    /** @var OutputInterface */
    protected $output;


    protected function configure()
    {
        $this->setName('necktie:rabbit:consumer')->setDescription('Start waiting for messages.')->addArgument(
                'consumer',
                InputArgument::REQUIRED,
                'Consumer name.'
            );
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $consumer = $input->getArgument('consumer');
        $this->output = $output;

        $server = $this->getContainer()->getParameter('rabbit_server');
        $port = $this->getContainer()->getParameter('rabbit_port');
        $user = $this->getContainer()->getParameter('rabbit_user');
        $password = $this->getContainer()->getParameter('rabbit_password');


        $connection = new AMQPStreamConnection('necktie.docker', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare($consumer, false, true, false, false);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

        $callback = function ($msg) use ($output) {
            $r = $this->process($msg);
            $this->handleProcessMessage($msg, $r);
            $output->writeln('['.$this->count.'] Message: '.$msg->body);
        };

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume($consumer, '', false, false, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }


    /**
     * @param AMQPMessage $msg
     * @param bool|string $processFlag
     */
    private function handleProcessMessage(AMQPMessage $msg, $processFlag)
    {
        if ($processFlag) {
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        }

        if (false == $processFlag && $this->count >= 10) {
            $msg->delivery_info['channel']->basic_reject($msg->delivery_info['delivery_tag'], false);
            $this->count = 0;
        } elseif (false == $processFlag) {
            $msg->delivery_info['channel']->basic_reject($msg->delivery_info['delivery_tag'], true);
            sleep(rand(5, 10));
            $this->count++;
        }

    }


    /**
     * @param AMQPMessage $msg
     * @return bool
     */
    private function process(AMQPMessage $msg)
    {
        $message = unserialize($msg->body);

        if (isset($message['header'])) {
            $header = $message['header'];
        } else {
            $header = [];
        }

        if (isset($message['body'])) {
            $body = $message['body'];
        } else {
            $body = null;
        }

        try {
            $responce = $this->getContainer()->get('neckie.gateway.api_gateway')->request(
                    $message['method'],
                    $message['url'],
                    $header,
                    $body
                );

            $this->addRecord($responce);
            if ($responce['status'] == 'error') {
                throw new \Exception($message['message']);
            }

        } catch (\Exception $ex) {

            $this->addRecord(['status' => 'error', 'message' => $ex->getMessage()]);

            return false;
        }

        return true;
    }


    public function addRecord($data)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        if (isset($data['status']) && $data['status'] == 'ok') {
            $log = $data['body'];
            $level = 200;
        } else {
            $log = $data['message'];
            $level = 500;
            $this->output->writeln($data['message']);
        }

        $sys = new SystemLog();
        $sys->setCreatedValue();

        $sys->setLevel($level);
        $sys->setLog($log);
        $sys->setUrl($data['url']);

        $em->persist($sys);
        $em->flush($sys);
    }

}