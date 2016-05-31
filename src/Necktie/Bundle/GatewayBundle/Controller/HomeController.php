<?php


namespace Necktie\Bundle\GatewayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class HomeController extends Controller
{

    public function indexAction()
    {
        $process = new Process('supervisorctl status');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $r = $process->getOutput();

        $r = explode(PHP_EOL, $r);

        return new JsonResponse($r);
    }

}