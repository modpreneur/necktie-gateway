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
     * @parem string $paySystem
     * @param string $type
     * @param string $vendor
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \LogicException
     */
    public function indexAction(Request $request, string $paySystem, string $type, string $vendor)
    {
        $this->get('payment.producer')->publish(
            serialize(
                [
                    'content' => $request->getContent(),
                    'vendor'  => $vendor,
                    'notificationType' => $type,    //ipn or webhook
                    'paySystem' => $paySystem,
                    'method'  => $request->getMethod(),
                ]
            )
        );

        return $this->json(['status' => 'ok'], 200);
    }

}