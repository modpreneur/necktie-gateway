<?php
/**
 * Created by PhpStorm.
 * User: polki
 * Date: 20.1.17
 * Time: 11:40
 */

namespace Necktie\GatewayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity()
 * @ORM\Table(name="decrypted_ipns")
 */
class DecryptedIpn
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
    private $decrypted;


    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $productsJson;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $shippingEmail;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $billingEmail;


    /**
     * DecryptedIpn constructor.
     * @param $decrypted
     */
    public function __construct($decrypted)
    {
        $this->decrypted = $decrypted;
        $arrayIpn = json_decode($decrypted,true );
//
        $this->productsJson = json_encode($arrayIpn['lineItems']);
        $this->shippingEmail = $arrayIpn['customer']['shipping']['email'];
        $this->billingEmail = $arrayIpn['customer']['billing']['email'];
//        $this->productsJson = "";
//        $this->billingEmail = "";
//        $this->shippingEmail = "";
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
    public function getDecrypted()
    {
        return $this->decrypted;
    }

    /**
     * @param string $decrypted
     */
    public function setDecrypted($decrypted)
    {
        $this->decrypted = $decrypted;
    }

    /**
     * @return string
     */
    public function getProductsJson()
    {
        return $this->productsJson;
    }

    /**
     * @param string $productsJson
     */
    public function setProductsJson($productsJson)
    {
        $this->productsJson = $productsJson;
    }

    /**
     * @return string
     */
    public function getShippingEmail()
    {
        return $this->shippingEmail;
    }

    /**
     * @param string $shippingEmail
     */
    public function setShippingEmail($shippingEmail)
    {
        $this->shippingEmail = $shippingEmail;
    }

    /**
     * @return string
     */
    public function getBillingEmail()
    {
        return $this->billingEmail;
    }

    /**
     * @param string $billingEmail
     */
    public function setBillingEmail($billingEmail)
    {
        $this->billingEmail = $billingEmail;
    }


}