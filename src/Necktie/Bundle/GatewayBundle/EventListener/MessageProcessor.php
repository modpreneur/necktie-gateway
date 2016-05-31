<?php


namespace Necktie\Bundle\GatewayBundle\EventListener;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Necktie\Bundle\GatewayBundle\Entity\Message;
use Necktie\Bundle\GatewayBundle\Gateway\ApiGateway;
use Necktie\Bundle\GatewayBundle\Logger\Logger;
use Symfony\Component\Console\Output\OutputInterface;
use Trinity\Bundle\BunnyBundle\Producer\Producer as BaseProducer;


/**
 * Class MessageProcessor
 * @package Necktie\Bundle\GatewayBundle\EventListener
 */
class MessageProcessor implements EventSubscriber
{

    /** @var  OutputInterface */
    protected $output;

    /** @var ApiGateway */
    protected $api;

    /** @var array */
    private $data = [];

    /** @var BaseProducer */
    private $producer;
    /**
     * @var Logger
     */
    private $logger;

    /** @var  EntityManager */
    private $manager;


    /**
     * MessageProcessor constructor.
     * @param ApiGateway $api
     * @param BaseProducer $producer
     * @param Logger $logger
     * @internal param EntityManager $manager
     */
    public function __construct(ApiGateway $api, BaseProducer $producer, Logger $logger)
    {
        $this->api = $api;
        $this->producer = $producer;
        $this->logger = $logger;
    }



    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'postPersist',
        ];
    }


    /**
     * @param LifecycleEventArgs $arg
     */
    public function postPersist(LifecycleEventArgs $arg)
    {
        $em = $arg->getEntityManager();
        $this->manager = $em;
        $this->logger->setManager($em);
        $entity = $arg->getEntity();

        if ($entity instanceof Message) {
            $this->processEntity($entity);
        }
    }


    /**
     * @param Message $entity
     */
    private function processEntity(Message $entity)
    {
        $data = $entity->getData();
        $result = $this->process($data);

        if ($result) {
            $this->manager->remove($entity);
            $this->manager->flush($entity);
        }
    }


    /**
     * @param array $message
     * @return bool
     */
    protected function process(array $message)
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

            $response = $this->api->request($message['method'], $message['url'], $header, $body, $tag, $attributes);
            
            $this->logger->addRecord($response);

            $this->data[] = $response;

            if ($response['status'] == 'error') {
                echo $response['message'].PHP_EOL;
                throw new \Exception($response['message']);
            }

            $this->producer->publish(serialize($response), 'necktie_exchange');


        } catch (\Exception $ex) {

            $error = [
                'status' => 'error',
                'url' => $message['url'],
                'message' => $ex->getMessage(),
            ];

            $this->logger->addRecord($error, 500);

            $this->producer->publish(serialize($error), 'necktie_exchange');

            return false;
        }

        return true;
    }


    public function setOutput($output)
    {
        $this->output = $output;
    }


}