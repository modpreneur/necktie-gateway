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

        if (isset($message['tag'])) {
            $tag = $message['tag'];
        } else {
            $tag = '';
        }

        if (isset($message['attributes'])) {
            $attributes = $message['attributes'];
        } else {
            $attributes = [];
        }

        try {
            $response = $this->getContainer()->get('necktie.gateway.api_gateway')->request(
                $message['method'],
                $message['url'],
                $header,
                $body,
                $tag,
                $attributes
            );

            $this->addRecord($response);
            $this->data[] = $response;

            $data = [];
            $data[] = $response;

            if ($response['status'] == 'error') {
                throw new \Exception($response['message']);
            }

        } catch (\Exception $ex) {

            $this->addRecord([
                'status' => 'error',
                'url' => $message['url'],
                'message' => $ex->getMessage()
            ]);

            if (strpos($ex->getMessage(), '400') !== false){
                $this->output->writeln('[' . (new \DateTime())->format(\DateTime::W3C) . '] <info> Error 400 </info>: ' . $ex->getMessage());
                return true;
            }

            $this->output->writeln('[' . (new \DateTime())->format(\DateTime::W3C) . '] <error> ' . $ex->getMessage() . ' </error>');
            return false;
        }

        return true;
    }


    public function addRecord($data)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        if (isset($data['status']) && $data['status'] == 'ok') {
            $log = json_encode($data);
            $level = 200;
        } else {
            $log = json_encode($data);
            $level = 500;
        }

        $sys = new SystemLog();
        $sys->setCreatedValue();

        $sys->setLevel($level);
        $sys->setLog($log);
        $sys->setUrl($data['url']);

        $em->persist($sys);
        $em->flush($sys);
        $this->output->writeln('[' . (new \DateTime())->format(\DateTime::W3C) . '] <info>Add record to system log.</info>');
    }



    protected function close()
    {
        $sender = $this->getContainer()->get('necktie.sender');
        $sender->sendToNecktie($this->data, $this->output);

        // clear
        $this->data = [];
        $this->output->writeln('[' . (new \DateTime())->format(\DateTime::W3C) . '] <info>Call: close().</info>');

        $this->counterMessages = 99999;
    }


}