<?php
/**
 * Created by PhpStorm.
 * User: polki
 * Date: 13.12.16
 * Time: 16:25
 */

namespace Necktie\GatewayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity()
 * @ORM\Table(name="ipns")
 */
class Ipn
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
    private $ipn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $savedAt;


    /**
     * Ipn constructor.
     * @param string $ipn
     */

    /**
     * @ORM\OneToMany(targetEntity="VendorIpn", mappedBy="vendor_urls")
     */
    private $VendorIpns;


    public function __construct($ipn)
    {
        $this->ipn = $ipn;
        $this->savedAt = new \DateTime();
        $this->repeat = 0;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getIpn()
    {
        return $this->ipn;
    }

    /**
     * @param string $ipn
     */
    public function setIpn($ipn)
    {
        $this->ipn = $ipn;
    }

    /**
     * @return \DateTime
     */
    public function getSavedAt()
    {
        return $this->savedAt;
    }

    /**
     * @param \DateTime $savedAt
     */
    public function setSavedAt($savedAt)
    {
        $this->savedAt = $savedAt;
    }

}