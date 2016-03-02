<?php


namespace Necktie\Bundle\GatewayBundle\EventListener;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Necktie\Bundle\GatewayBundle\Entity\Message;
use Necktie\Bundle\GatewayBundle\Entity\SystemLog;
use Necktie\Bundle\GatewayBundle\Gateway\ApiGateway;
use Necktie\Bundle\GatewayBundle\RabbitMQ\Producer;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class MessageProcessor
 * @package Necktie\Bundle\GatewayBundle\EventListener
 */
class MessageProcessor
{

    /** @var  OutputInterface */
    protected $output;

    /** @var ApiGateway */
    protected $api;

    /** @var EntityManager */
    private $manager;

    /** @var array */
    private $data = [];

    /** @var Producer */
    private $producer;


    /**
     * MessageProcessor constructor.
     * @param ApiGateway $api
     * @param Producer $producer
     * @param EntityManager $manager
     */
    public function __construct(ApiGateway $api, Producer $producer, EntityManager $manager = null)
    {
        $this->api      = $api;
        $this->manager  = $manager;
        $this->producer = $producer;
    }


    /**
     * @param LifecycleEventArgs $arg
     */
    public function postPersist(LifecycleEventArgs $arg)
    {
        $em = $arg->getEntityManager();
        $this->manager = $em;
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
            $response = $this->api->request(
                $message['method'],
                $message['url'],
                $header,
                $body,
                $tag,
                $attributes
            );

            $this->addRecord($response);
            $this->data[] = $response;

            $data   = [];
            $data[] = $response;

            if ($response['status'] == 'error') {
                throw new \Exception($response['message']);
            }

        } catch (\Exception $ex) {
            $this->addRecord(
                [
                    'status' => 'error',
                    'url' => $message['url'],
                    'message' => $ex->getMessage(),
                ],
                500
            );

            return false;
        }

        return true;
    }


    /**
     * @param array $data
     * @param int $level
     */
    public function addRecord($data, $level = 200)
    {
        $em = $this->manager;

        $this->producer->publish('gateway', $data, 'gateway_exchange');

        if (isset($data['status']) && $data['status'] == 'ok') {
            $log = json_encode($data);
        } else {
            $log = json_encode($data);
        }

        $sys = new SystemLog();
        $sys->setCreatedValue();

        $sys->setLevel($level);
        $sys->setLog($log);
        $sys->setUrl($data['url']);

        $em->persist($sys);
        $em->flush($sys);
    }


    public function setOutput($output)
    {
        $this->output = $output;
    }


}