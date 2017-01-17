<?php

namespace Necktie\GatewayBundle\Controller;

use Necktie\GatewayBundle\Entity\Ipn;
use Necktie\GatewayBundle\Entity\VendorIpn;
use Necktie\GatewayBundle\Entity\VendorUrl;
use OAuth\Common\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PaymentController
 * @package Necktie\GatewayBundle\Controller
 *
 * @Route("/payment")
 */
class PaymentController extends Controller
{
    
    /**
     * @Route("/{paySystem}/{type}/{vendor}")
     * @param Request $request
     * @param string $paySystem
     * @param string $type
     * @param string $vendor
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \LogicException
     */
    public function indexAction(Request $request, string $paySystem, string $type, string $vendor)
    {
        $content = [
            'content' => $request->getContent(),
            'vendor'  => $vendor,
            'notificationType' => $type,    //ipn or webhook
            'paySystem' => $paySystem,
//            'method'  => $request->getMethod(), // should be only POST
            'method'  => 'POST', // should be only POST
        ];

        $em = $this->getDoctrine()->getManager();
        $ipn = new Ipn($request->getContent());
        $em->persist($ipn);
        $em->flush();


        $this->getDoctrine()->getManager();
        $vendorUrls = $em->getRepository('GatewayBundle:VendorUrl')->findBy(
            ['vendor' => $vendor],
            ['priority' => 'DESC']
        );
//        $vendorUrls = $em->getRepository('GatewayBundle:VendorUrl')->findAll();
//        dump($vendorUrls);
        $this->get('gw.message.processor')->addProcessor($this->get('gw.processor.httpcheck'));
        /** @var VendorUrl $vendorUrl */
        foreach ($vendorUrls as $vendorUrl) {
            $vendorIpn = new VendorIpn($vendorUrl, $ipn);
            $em->persist($vendorIpn);
            $em->flush();

            $message = [
                'method' => 'POST',
//            'method'  => $request->getMethod(), // should be only POST,
                'url' => $vendorUrl->getUrl(),
                'header' => [],
                'body' => $content,
                'tag' => '',
                'attributes' => ['vendorIpnId' => $vendorIpn->getId()],
                'processorName' => 'HTTPCheckProcessor'
            ];
            $this->get('payment_redirect.producer')->publish(
                serialize($message)
            );

        }

        return $this->json(['message' => 'OK '], 200);
//        return $this->json($message, 200);
    }
}
