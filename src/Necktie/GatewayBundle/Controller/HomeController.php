<?php

namespace Necktie\GatewayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Class HomeController
 * @package Necktie\GatewayBundle\Controller
 */
class HomeController extends Controller
{

    //@todo @TomasJancar -> secure supervisor

    /**
     * @return JsonResponse
     */
    public function indexAction()
    {
        $process = new Process('supervisorctl -c /var/app/supervisor/supervisord.conf status');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $r = $process->getOutput();

        $r = explode(PHP_EOL, $r);

        return new JsonResponse($r);
    }


    /**
     * @return JsonResponse
     */
    public function restartAction(){
        $process = new Process('supervisorctl -c /var/app/supervisor/supervisord.conf restart all');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $r = $process->getOutput();
        $r = explode(PHP_EOL, $r);

        return new JsonResponse($r);
    }
}