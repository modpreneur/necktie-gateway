<?php
/**
 * Created by PhpStorm.
 * User: gabriel
 * Date: 2.3.17
 * Time: 13:25
 */

namespace Necktie\GatewayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class PaymentLog
 * @package Necktie\GatewayBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="payment_log")
 */
class PaymentLog
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;


    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $log;


    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $url;


    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * PaymentLog constructor.
     *
     * @param $log string
     * @param $url string
     */
    public function __construct(string $url, string $log)
    {
        $this->createdAt = new \DateTime();
        $this->url = $url;
        $this->log = $log;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }


    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }


    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
