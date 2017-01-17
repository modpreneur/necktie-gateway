<?php

namespace Necktie\GatewayBundle\Gateway\RequestProcessor;


use Doctrine\ORM\EntityManager;
use Exception;
use Necktie\GatewayBundle\Entity\VendorIpn;
use Necktie\GatewayBundle\Gateway\ApiGateway;
use Necktie\GatewayBundle\Logger\Logger;

/**
 * Class HTTPFilter
 * @package Necktie\GatewayBundle\Gateway\Filters
 */
class HTTPCheckProcessor extends BaseProcessor
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

    public function process(array $message = [])
    {

        if (isset($message['header'])) {
            $header = $message['header'];
        } else {
            $header = [];
        }

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
            $vendorIpn->setSent(new \DateTime());
            $this->em->persist($vendorIpn);
            $this->em->flush();

            $response = $this
                ->gateway
                ->request(
                    $message['method'],
                    $message['url'],
                    $header,
                    $body,
                    $tag,
                    $attributes
                );

            $this->logger->addRecord($response);

            if ($response['status'] == 'error') {
                echo $response['response'] . PHP_EOL;
                $this->logger->addRecord($response);
                throw new \RuntimeException($response['response']);
            }

            $vendorIpn->setConfirmed(new \DateTime());
            $this->em->persist($vendorIpn);
            $this->em->flush();

            return [
                'status'     => $response['status'].'3',
                'response'   => $response['response'],
                'url'        => $message['url'],
                'tag'        => $tag,
                'attributes' => $attributes
            ];
        } catch (Exception $ex) {
            $repeat = false;
            if ($vendorIpn->getRepeated() < 20) {
                $vendorIpn->setRepeated($vendorIpn->getRepeated()+1);
                $this->em->persist($vendorIpn);
                $this->em->flush();
                $repeat = true;
            }

            $error =  [
                'status'     => 'error',
                'response'   => $ex->getMessage(),
                'url'        => $message['url'],
                'tag'        => $tag,
                'attributes' => $attributes,
                'repeat'     => $repeat
            ];

            $this->logger->addRecord($error, 500);
            return $error;
        }
    }

}