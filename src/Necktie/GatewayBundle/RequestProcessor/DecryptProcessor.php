<?php
/**
 * Created by PhpStorm.
 * User: polki
 * Date: 20.1.17
 * Time: 11:29
 */

namespace Necktie\GatewayBundle\Gateway\RequestProcessor;

use Doctrine\ORM\EntityManager;
use Exception;
use Necktie\GatewayBundle\Entity\DecryptedIpn;
use Necktie\GatewayBundle\Entity\VendorIpn;
use Necktie\GatewayBundle\Entity\VendorUrl;
use Necktie\GatewayBundle\Gateway\ApiGateway;
use Necktie\GatewayBundle\Logger\Logger;
use SebastianBergmann\CodeCoverage\RuntimeException;

/**
 * Class HTTPFilter
 * @package Necktie\GatewayBundle\Gateway\Filters
 */
class DecryptProcessor extends BaseProcessor
{
    /** @var  EntityManager */
    protected $em;

    /**
     * @var ApiGateway
     */
    protected $gateway;

    /**
     * HTTPFilter constructor.
     * @param EntityManager $em
     * @param ApiGateway $apiGateway
     * @param Logger $logger
     */
    public function __construct(EntityManager $em, ApiGateway $apiGateway, Logger $logger)
    {
        $this->em = $em;
        parent::__construct($logger);
        $this->gateway = $apiGateway;
    }

//    public function encryptIPN(VendorUrl $vendor, IPN $ipn)
//    {
//        $crypted = base64_encode(mcrypt_encrypt(
//            MCRYPT_RIJNDAEL_128,
//            substr(sha1($vendor->getSecretId()), 0, 32),
//            $ipn->getStringRepresentation(),
//            MCRYPT_MODE_CBC,
//            base64_decode($ipn->getIv())));
//
//        if($crypted === false)
//            return "cant encrypt";
//
//        $finalObject = array();
//        $finalObject["notification"] = $crypted;
//        $finalObject["iv"] = $ipn->getIv();
//
//        return json_encode($finalObject);
//    }

    public function decryptIPN(VendorUrl $vendor, $ipn)
    {

        $ipnJson = json_decode($ipn, true);
        $encrypted = $ipnJson["notification"];
        $iv = $ipnJson["iv"];

        $decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128,
            substr(sha1($vendor->getSecretId()), 0, 32),
            base64_decode($encrypted),
            MCRYPT_MODE_CBC,
            base64_decode($iv)), "\0..\32");


        return new DecryptedIpn(utf8_encode($decrypted));
    }


    public function process(array $message = [])
    {
        if (isset($message['body'])) {
            $body = $message['body'];
        } else {
            $body = null;
        }

        if (isset($message['tag'])) {
            $tag = $message['tag'];
        } else {
            $tag = '';
        }

        if (isset($message['attributes'])) {
            $attributes = $message['attributes'];
        } else {
            $attributes = [];
        }

        if(array_key_exists('vendorIpnId', $attributes)) {
            /** @var VendorIpn $vendorIpn */
            $vendorIpn = $this->em->getRepository('GatewayBundle:VendorIpn')->find($attributes['vendorIpnId']);
        } else {
            return ['status' => 'IpnNotFound'];
        }

        try {

            $decryptedIpn = $this->decryptIPN($vendorIpn->getVendorUrl(), $body);

            $this->em->persist($decryptedIpn);
            $this->em->flush();

            return [
                'status'     => 'ok',
                'url'        => $message['url'],
                'tag'        => $tag,
                'attributes' => $attributes
            ];
        } catch (Exception $ex) {

            $error =  [
                'status'     => 'error',
                'response'   => $ex->getMessage(),
                'url'        => $message['url'],
                'tag'        => $tag,
                'attributes' => $attributes,
            ];

            $this->logger->addRecord($error, 101);
//            echo "ahoj\n";
            return $error;
        }
    }


}