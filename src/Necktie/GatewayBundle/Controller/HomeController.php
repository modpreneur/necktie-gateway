<?php

namespace Necktie\GatewayBundle\Controller;

use fXmlRpc\Exception\FaultException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Supervisor\Supervisor;
use Supervisor\Connector\XmlRpc;
use fXmlRpc\Client as ClientRpc;
use GuzzleHttp\Client;
use fXmlRpc\Transport\HttpAdapterTransport;
use Http\Adapter\Guzzle6\Client as ClientGuzzle;
use Http\Message\MessageFactory\DiactorosMessageFactory;
use Trinity\Bundle\LoggerBundle\Services\ElasticReadLogService;


/**
 * Class HomeController
 * @package Necktie\GatewayBundle\Controller
 */
class HomeController extends Controller
{
    protected $supervisor;

    /** @var  string */
    private $error;


    /**
     * HomeController constructor.
     *
     */
    public function __construct()
    {
        $this->supervisor = new Supervisor($this->getConnector());
    }


    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \LogicException
     */
    public function indexAction() : Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('gateway_dashboard');
        }

        return $this->render('@Gateway/Login/login.html.twig', []);
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function dashboardAction() : Response
    {
        $rabbit = $this->get('gw.rabbitmq.reader');
        $rabbit->process();

        return $this->render('GatewayBundle:Home:index.html.twig', [
            'version'     => \exec('git rev-parse --verify HEAD'),
            'datetime'    => new \DateTime(),
            'rabbitUrl'   => $this->getParameter('rabbit_url'),
            'rabbitPort'  => $this->getParameter('rabbit_port'),
            'rabbitManagerPort'  => $this->getParameter('rabbit_manager_port'),
            'rabbit'      => $rabbit,
            'rabbitError' => $rabbit->getConnectionError(),
            'elasticUri'  => $this->getParameter('elastic_host'),
            'elasticIsOk' => $this->checkElastic(),
            'error'       => $this->getError(),
            'processes'   => $this->getProcesses(),
            'necktieUrl' => $this->getParameter('necktie_url'),
        ]);
    }


    /**
     * @return Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function statusAction() : Response
    {
        $checkDashboard = $this->dashboardAction();

        if ($checkDashboard->getStatusCode() !== 200) {
            return new Response('Dashboard has error', $checkDashboard->getStatusCode());
        }

        $rabbit = $this->get('gw.rabbitmq.reader');
        $rabbit->process();

        if ($rabbit->getConnectionError() !== '') {
            return new Response($rabbit->getConnectionError(), 500);
        }

        foreach ($this->getProcesses() as $process) {
            /** @var $process \Supervisor\Process */
            if (false === $process->isRunning()) {
                //return new Response((string)$process . 'has error', 500);
            }
        }

        return new Response('All is fine');
    }


    /**
     * @return Response
     * @throws \LogicException
     */
    public function rabbitAction() : Response
    {
        $rabbit = $this->get('gw.rabbitmq.reader');
        $rabbit->process();

        return $this->render('@Gateway/Home/rabbitmq.html.twig', [
            'rabbit'      => $rabbit,
            'necktieUrl' => $this->getParameter('necktie_url'),
            'rabbitError' => $rabbit->getError(),
        ]);
    }


    /**
     * @return Response
     */
    public function loggerAction() : Response
    {
        /** @var ElasticReadLogService $elReader */
        $elReader = $this->get('trinity.logger.elastic_read_log_service');
        $loggers  = $elReader->getMatchingEntities('Logger');

        return $this->render('@Gateway/Home/logger.html.twig', [
            'necktieUrl' => $this->getParameter('necktie_url'),
            'loggers' => $loggers,
        ]);
    }


    /**
     * @return Response
     * @throws \LogicException
     */
    public function supervisorAction() : Response
    {
        $rabbit = $this->get('gw.rabbitmq.reader');
        $rabbit->process();

        return $this->render('@Gateway/Home/supervisor.html.twig', [
            'supervisorCommands' => $this->getProcesses(),
            'supervisorGroup' => $this->getParameter('rabbit_supervisor_group'),
            'necktieUrl' => $this->getParameter('necktie_url'),
            'rabbitError' => $rabbit->getError(),
        ]);
    }


    /**
     * @return Response
     */
    public function messagesAction() : Response
    {
        /** @var ElasticReadLogService $elReader */
        $elReader = $this->get('trinity.logger.elastic_read_log_service');
        $messages = $elReader->getMatchingEntities('Message');

        return $this->render('@Gateway/Home/messages.html.twig', [
            'necktieUrl' => $this->getParameter('necktie_url'),
            'messages'   => $messages,
        ]);
    }


    /**
     * @return Response
     */
    public function requestsAction()
    {
        /** @var ElasticReadLogService $elReader */
        $elReader = $this->get('trinity.logger.elastic_read_log_service');
        $requests = $elReader->getMatchingEntities('Request');

        return $this->render('@Gateway/Home/requests.html.twig', [
            'necktieUrl' => $this->getParameter('necktie_url'),
            'requests' => $requests
        ]);
    }


    /**
     * @param string $group
     * @param string $name
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function stopProcessAction(string $group, string $name) : RedirectResponse
    {
        $this->supervisor->stopProcess($group . ':' . $name, 10);

        return $this->redirectToRoute('gateway_supervisor');
    }


    /**
     * @param string $group
     * @param string $name
     *
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function processLogsAction(string $group, string $name) : Response
    {
        $result = '';

        foreach ($this->supervisor->tailProcessStdoutLog($group . ':' . $name, 0, 10000) as $item) {
            $result .= $item . PHP_EOL;
        }

        return new Response($result);
    }


    /**
     * @param string $group
     * @param string $name
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cleanLogAction(string $group, string $name) : RedirectResponse
    {
        $this->supervisor->clearProcessLogs($group . ':' . $name);

        return $this->redirectToRoute('gateway_supervisor');
    }


    /**
     * @param string $group
     * @param string $name
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \InvalidArgumentException
     */
    public function startProcessAction(string $group, string $name) : Response
    {
        try {
            $this->supervisor->startProcess($group . ':' . $name, 10);
        } catch (FaultException $exception) {
            return new Response('ERROR: ' . $exception->getMessage());
        }

        return $this->redirectToRoute('gateway_supervisor');
    }


    /**
     * @return RedirectResponse
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     * @throws \Symfony\Component\Process\Exception\LogicException
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     */
    public function restartAction() : RedirectResponse
    {
        $process = new Process('supervisorctl -c /var/app/supervisor/supervisord.conf restart all');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        //$r = $process->getOutput();
        $r = $this->getProcesses();

        return $this->redirectToRoute('gateway');
    }


    /**
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function stopAllProcessesAction(Request $request) : Response
    {
        $token = $request->get('token');
        if ($token !== $this->getParameter('supervisor_api_token'))
            return new Response('Token is not valid', 400);

        $this->supervisor->stopAllProcesses();

        return new Response('ok');
    }


    /**
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function startAllProcessesAction(Request $request) : Response
    {
        $token = $request->get('token');
        if ($token !== $this->getParameter('supervisor_api_token'))
            return new Response('Token is not valid', 400);

        $this->supervisor->startAllProcesses();

        return new Response('ok');
    }


    /**
     * @return \Supervisor\Process[]
     */
    private function getProcesses() : array
    {
        return $this->supervisor->getAllProcesses();
    }


    /**
     * @return XmlRpc
     */
    private function getConnector(): XmlRpc
    {
        $httpClient = new Client(['auth' => ['user', '123']]);

        $client = new ClientRpc(
            'localhost' . ':' . '9005' . '/RPC2',
            new HttpAdapterTransport(
                new DiactorosMessageFactory(),
                new ClientGuzzle($httpClient)
            )
        );

        return new XmlRpc($client);
    }


    /**
     * @return bool
     */
    private function checkElastic() : bool
    {
        return true;

        $client = new Client();
        try {
            $result = $client->get($this->getParameter('elastic_host'));
            return $result->getStatusCode() === 200;
        } catch (ConnectException | ServerException $e) {
            $this->error = ($e->hasResponse())? $e->getResponse()->getBody() : null;
            return false;
        }
    }


    /**
     * @return string
     */
    private function getError() : ?string
    {
        return $this->error;
    }
}
