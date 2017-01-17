<?php
/**
 * Created by PhpStorm.
 * User: polki
 * Date: 13.12.16
 * Time: 16:56
 */

namespace Necktie\GatewayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity()
 * @ORM\Table(name="vendor_ipns")
 */
class VendorIpn
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="VendorUrl", inversedBy="vendor_ipns")
     * @ORM\JoinColumn(name="vendor_url_id", referencedColumnName="id")
     */
    private $vendorUrl;

    /**
     * @ORM\ManyToOne(targetEntity="Ipn", inversedBy="vendor_ipns")
     * @ORM\JoinColumn(name="ipn_id", referencedColumnName="id")
     */
    private $ipn;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $repeated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $confirmed;



    /**
     * VendorIpn constructor.
     * @param $vendorUrl
     * @param $ipn
     */
    public function __construct($vendorUrl, $ipn)
    {
        $this->vendorUrl = $vendorUrl;
        $this->ipn = $ipn;
        $this->repeated = 0;

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
     * @return mixed
     */
    public function getVendorUrl()
    {
        return $this->vendorUrl;
    }

    /**
     * @param mixed $vendorUrl
     */
    public function setVendorUrl($vendorUrl)
    {
        $this->vendorUrl = $vendorUrl;
    }

    /**
     * @return mixed
     */
    public function getIpn()
    {
        return $this->ipn;
    }

    /**
     * @param mixed $ipn
     */
    public function setIpn($ipn)
    {
        $this->ipn = $ipn;
    }

    /**
     * @return int
     */
    public function getRepeated()
    {
        return $this->repeated;
    }

    /**
     * @param int $repeated
     */
    public function setRepeated($repeated)
    {
        $this->repeated = $repeated;
    }


    /**
     * @return \DateTime
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * @param \DateTime $sent
     */
    public function setSent($sent)
    {
        $this->sent = $sent;
    }

    /**
     * @return \DateTime
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * @param \DateTime $confirmed
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;
    }

    public function repeat() {
        $this->repeat++;
    }


}