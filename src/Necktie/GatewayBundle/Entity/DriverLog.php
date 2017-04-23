<?php

namespace Necktie\GatewayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class DriverLog
 * @package Necktie\GatewayBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="driver_log")
 */
class DriverLog
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $driver;

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
    public function __construct(string $url, string $log, string $driver)
    {
        $this->createdAt = new \DateTime();
        $this->url = $url;
        $this->log = $log;
        $this->driver = $driver;
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

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }
}
