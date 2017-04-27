<?php

namespace Necktie\GatewayBundle\Controller;

use Necktie\GatewayBundle\Entity\DriverLog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DriverController
 * @package Necktie\GatewayBundle\Controller
 *
 * @Route("/driver")
 */
class DriverController extends Controller
{
    const DEV_TAG = 'dev_';

    /**
     * @Route("/{driver}")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \LogicException
     */
    public function indexAction(Request $request, string $driver)
    {
        $content = [
            'content' => $request->getContent(),
            'driver'  => $driver,
            'method'  => $request->getMethod(), // should be only POST
        ];

        $this->get('driver.producer')->publish(
            json_encode($content)
        );

        return $this->json(['message' => 'OK'], 200);
    }
}
