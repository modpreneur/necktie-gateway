<?php


namespace Necktie\Bundle\GatewayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 *
 * @ORM\Entity()
 * @ORM\Table(name="messages")
 */
class Message
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $deliveredAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $deliveryTag;


    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }


    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }


    /**
     * @return mixed
     */
    public function getData()
    {
        return unserialize($this->message);
    }


    /**
     * @return \DateTime
     */
    public function getDeliveredAt()
    {
        return $this->deliveredAt;
    }


    /**
     * @param \DateTime $deliveredAt
     */
    public function setDeliveredAt($deliveredAt)
    {
        $this->deliveredAt = $deliveredAt;
    }


    /**
     * @return string
     */
    public function getDeliveryTag()
    {
        return $this->deliveryTag;
    }


    /**
     * @param string $deliveryTag
     */
    public function setDeliveryTag($deliveryTag)
    {
        $this->deliveryTag = $deliveryTag;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

}