<?php

namespace Necktie\Bundle\GatewayBundle\Command;

use Necktie\Bundle\GatewayBundle\Entity\SystemLog;

use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Input\InputArgument;


/**
 * Class ConsumerCommand
 * @package Necktie\Bundle\GatewayBundle\Command
 */
class ConsumerCommand extends BaseCommnad
{

    protected $data = [];


    protected function configure(){
        parent::configure();

        $this
            ->setName('necktie:rabbit:consumer')
            ->setDescription('Start waiting for messages.')
            ->addArgument(
                'consumer',
                InputArgument::REQUIRED,
                'Consumer name.'
            );
    }


    /**
     * @param AMQPMessage $msg
     * @return bool
     */
    public function process(AMQPMessage $msg)
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
            $this->data[] = $responce;

            if ($responce['status'] == 'error') {
                throw new \Exception($responce['message']);
            }

        } catch (\Exception $ex) {

            $this->addRecord([
                'status' => 'error',
                'url' => $message['url'],
                'message' => $ex->getMessage()
            ]);

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


    protected function close()
    {
        $sender = $this->getContainer()->get('necktie.sender');
        $sender->sendToNecktie($this->data);
    }


}