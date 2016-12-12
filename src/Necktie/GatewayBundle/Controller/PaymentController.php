<?php

namespace Necktie\GatewayBundle\Controller;

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
            'method'  => $request->getMethod(), // should be only POST
        ];

        $message = [
            'method' => $request->getMethod(),
            'url' => '',
            'header' => '',
            'body' => $content,
            'tag' => '',
            'attributes' => ''

        ];

        switch ($vendor) {
            case 'v1':
                $message['url'] = 'abc';
                $this->get('payment_redirect.producer')->publish(
                    serialize($message)
                );
                break;
            case 'v2':
                $message['url'] = 'abc1';
                $this->get('payment_redirect.producer')->publish(
                    serialize($message)
                );
                $message['url'] = 'abc2';
                $this->get('payment_redirect.producer')->publish(
                    serialize($message)
                );
                break;
            case 'v3':
                $message['url'] = 'abc3';
                $this->get('payment_redirect.producer')->publish(
                    serialize($message)
                );
                $message['url'] = 'abc4';
                $this->get('payment_redirect.producer')->publish(
                    serialize($message)
                );
                $message['url'] = 'abc5';
                $this->get('payment_redirect.producer')->publish(
                    serialize($message)
                );
                break;
        }
//        $this->get('payment.producer')->publish(
//            serialize($content)
//        );
        return $this->json(['message' => 'OK'], 200);
    }
}
