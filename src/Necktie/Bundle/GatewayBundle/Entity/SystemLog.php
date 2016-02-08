<?php

namespace Necktie\Bundle\GatewayBundle\Entity;

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
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $url;



    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->modified = new \DateTime();

        $this->created = new \DateTime();
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
     * Get log
     *
     * @return string
     */
    public function getLog()
    {
        return $this->log;
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
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
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
    public function getCreated()
    {
        return $this->created;
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