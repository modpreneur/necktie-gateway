<?php

namespace Necktie\GatewayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity()
 * @ORM\Table(name="system_log")
 */
class SystemLog
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $log;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $level;


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


    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get log
     *
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }


    /**
     * Set log
     *
     * @param string $log
     * @return SystemLog
     */
    public function setLog($log)
    {
        $this->log = $log;

        return $this;
    }


    /**
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }


    /**
     * Set level
     *
     * @param string $level
     * @return SystemLog
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }


    /**
     * Set created
     *
     * @param \DateTime $created
     * @return SystemLog
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }


    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }


    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }


    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }


    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

}