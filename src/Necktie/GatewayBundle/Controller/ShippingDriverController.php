<?php

namespace Necktie\GatewayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ShippingDriverController
 * @package Necktie\GatewayBundle\Controller
 *
 * @Route("/shipping-driver")
 */
class ShippingDriverController extends Controller
{
    const DEV_TAG = 'dev_';

    /**
     * @Route("/{shippingDriver}")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \LogicException
     */
    public function indexAction(Request $request, string $shippingDriver)
    {
        $content = [
            'content' => $request->getContent(),
            'shippingDriver'  => $shippingDriver,
            'method'  => $request->getMethod(), // should be only POST
        ];

        $this->get('shipping_driver.producer')->publish(
            json_encode($content)
        );

        return $this->json(['message' => 'OK'], 200);
    }
}
