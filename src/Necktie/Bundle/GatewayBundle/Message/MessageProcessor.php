<?php


namespace Necktie\Bundle\GatewayBundle\Message;


use Doctrine\ORM\EntityManager;
use Necktie\Bundle\GatewayBundle\Event\MessageEvent;
use Necktie\Bundle\GatewayBundle\Gateway\ApiGateway;
use Necktie\Bundle\GatewayBundle\Logger\Logger;
use Necktie\Bundle\GatewayBundle\Proxy\ProducerProxy;


class MessageProcessor
{

    /** @var  EntityManager */
    protected $em;


    /**
     * @var Logger
     */
    protected $logger;


    /**
     * @var ApiGateway
     */
    private $gateway;
    /**
     * @var ProducerProxy
     */
    private $producer;


    /**
     * MessageProcessor constructor.
     * @param EntityManager $em
     * @param ApiGateway $gateway
     * @param Logger $logger
     * @param ProducerProxy $producer
     */
    public function __construct(EntityManager $em, ApiGateway $gateway, Logger $logger, ProducerProxy $producer)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->gateway = $gateway;
        $this->producer = $producer;
    }


    public function process(MessageEvent $messageEvent)
    {
        $this->execute($messageEvent->getContent());
    }


    /**
     * @param array $message
     * @return bool
     */
    protected function execute(array $message)
    {
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

            $response = $this->gateway->request($message['method'], $message['url'], $header, $body, $tag, $attributes);

            $this->logger->addRecord($response);

            $this->data[] = $response;

            if ($response['status'] == 'error') {
                echo $response['message'].PHP_EOL;
                throw new \Exception($response['message']);
            }

            $this->producer->publish(serialize($response), 'necktie');


        } catch (\Exception $ex) {
            $error = [
                'status' => 'error',
                'url' => $message['url'],
                'message' => $ex->getMessage(),
            ];

            $this->logger->addRecord($error, 500);
            $this->producer->publish(serialize($error), 'necktie');

            return false;
        }

        return true;
    }

}