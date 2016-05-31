<?php


namespace Necktie\Bundle\GatewayBundle\EventListener;


use Doctrine\ORM\EntityManager;
use Necktie\Bundle\GatewayBundle\Entity\Message;
use Trinity\Bundle\BunnyBundle\Event\RabbitMessageConsumedEvent;

class MessagesListener
{

    /** @var  EntityManager */
    protected $em;


    /**
     * MessagesListener constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    public function onConsume(RabbitMessageConsumedEvent $consumedEvent)
    {

        echo 'Consume message from '.$consumedEvent->getSourceQueue().PHP_EOL;

        $msg = $consumedEvent->getMessage();
        $em = $this->em;

        $em->beginTransaction();
        try {
            // @todo log content
            $message = new Message();
            $message->setMessage($msg->content);
            $message->setDeliveredAt(new \DateTime());
            $message->setDeliveryTag($msg->deliveryTag);

            $em->persist($message);
            $em->flush($message);
            $em->commit();
            echo 'Loged message from '.$consumedEvent->getSourceQueue().PHP_EOL;

        } catch (\Exception $ex) {
            $em->rollback();
            //@todo - log error
            throw $ex;
        }

    }

}