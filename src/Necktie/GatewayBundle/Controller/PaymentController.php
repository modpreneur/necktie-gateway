<?php

namespace Necktie\GatewayBundle\Controller;

use Necktie\GatewayBundle\Entity\PaymentLog;
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
    const DEV_TAG = 'dev_';
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
        if (0 === strpos($type, self::DEV_TAG)) {
            $type = substr($type, strlen(self::DEV_TAG));
            $log = new PaymentLog(
                $request->getRequestUri(), //contains type, and vendor
                $request->getContent()
            );
            $this->getDoctrine()->getManager()->persist($log);
            $this->getDoctrine()->getManager()->flush();
        }

        $content = [
            'content' => $request->getContent(),
            'vendor'  => $vendor,
            'notificationType' => $type,    //ipn or webhook
            'paySystem' => $paySystem,
            'method'  => $request->getMethod(), // should be only POST
        ];
        $this->get('payment.producer')->publish(
            json_encode($content)
        );
        return $this->json(['message' => 'OK'], 200);
    }
}
