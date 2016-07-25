<?php


namespace Necktie\GatewayBundle\Message;


use Doctrine\ORM\EntityManager;
use Necktie\GatewayBundle\Entity\Message;
use Necktie\GatewayBundle\Event\MessageEvent;


/**
 * Class MessagesLogger
 * @package Necktie\GatewayBundle\Message
 */
class MessagesLogger
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


    public function saveMessage(MessageEvent $messageEvent)
    {
        $em = $this->em;

        $em->beginTransaction();
        try {
            // @todo log content
            $message = new Message();
            $message->setMessage(json_encode($messageEvent->getContent()));
            $message->setDeliveredAt(new \DateTime());
            $message->setDeliveryTag($messageEvent->getDeliveryTag());

            $em->persist($message);
            $em->flush($message);
            $em->commit();
            echo 'Logged message from '.$messageEvent->getQueue().PHP_EOL;

        } catch (\Exception $ex) {
            $em->rollback();
            //@todo - log error
            throw $ex;
        }
    }

}