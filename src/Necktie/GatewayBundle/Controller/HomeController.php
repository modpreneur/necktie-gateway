<?php

namespace Necktie\GatewayBundle\Controller;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Necktie\GatewayBundle\Entity\Message;
use Necktie\GatewayBundle\Entity\SystemLog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
use Trinity\Bundle\LoggerBundle\Entity\ExceptionLog;
use Trinity\Bundle\LoggerBundle\Services\ElasticLogService;
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
     */
    public function indexAction()
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('gateway_dashboard');
        }

        return $this->render('@Gateway/Login/login.html.twig', []);
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashboardAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $messages = $em
            ->getRepository(Message::class)
            ->findBy([], ['id' => 'desc'], 20);

        $systemLogs = $em
            ->getRepository(SystemLog::class)
            ->findBy([], ['id' => 'desc'], 20);

        $rabbit = $this->get('gw.rabbitmq.reader');
        $rabbit->process();

        return $this->render('GatewayBundle:Home:index.html.twig', [
            'version'    => exec("git rev-parse --verify HEAD"),
            'datetime'   => new \DateTime(),
            'rabbitUrl'  => $this->getParameter('rabbit_url'),
            'rabbitPort'  => $this->getParameter('rabbit_port'),
            'rabbitManagerPort'  => $this->getParameter('rabbit_manager_port'),
            'rabbit'     => $rabbit,
            'rabbitError' => $rabbit->getConnectionError(),
            'elasticUri'  => $this->getParameter('elastic_host'),
            'elasticIsOk' => $this->checkElastic(),
            'error'       => $this->getError(),
        ]);
    }


    /**
     * @return Response
     */
    public function rabbitAction() : Response
    {
        $rabbit = $this->get('gw.rabbitmq.reader');
        $rabbit->process();

        return $this->render('@Gateway/Home/rabbitmq.html.twig', [
            'rabbit'      => $rabbit,
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
        $loggers = $elReader->getMatchingEntities('Logger');

        return $this->render('@Gateway/Home/logger.html.twig', [
            'loggers' => $loggers,
        ]);
    }


    /**
     * @return Response
     */
    public function supervisorAction()
    {
        $rabbit = $this->get('gw.rabbitmq.reader');
        $rabbit->process();

        return $this->render('@Gateway/Home/supervisor.html.twig', [
            'supervisorCommands' => $this->getProcesses(),
            'supervisorGroup' => $this->getParameter('rabbit_supervisor_group'),
            'rabbitError' => $rabbit->getError(),
        ]);
    }


    /**
     * @return Response
     */
    public function messagesAction()
    {
        /** @var ElasticReadLogService $elReader */
        $elReader = $this->get('trinity.logger.elastic_read_log_service');
        $messages = $elReader->getMatchingEntities('Message');

        return $this->render('@Gateway/Home/messages.html.twig', [
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
            'requests' => $requests
        ]);
    }


    /**
     * @param string $group
     * @param string $name
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function stopProcessAction(string $group, string $name)
    {
        $this->supervisor->stopProcess($group . ':' . $name, 10);

        return $this->redirectToRoute('gateway_supervisor');
    }


    /**
     * @param string $group
     * @param string $name
     *
     * @return Response
     */
    public function processLogsAction(string $group, string $name)
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
    public function cleanLogAction(string $group, string $name)
    {
        $this->supervisor->clearProcessLogs($group . ':' . $name);
        return $this->redirectToRoute('gateway_supervisor');
    }


    /**
     * @param string $group
     * @param string $name
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function startProcessAction(string $group, string $name)
    {
        $this->supervisor->startProcess($group . ':' . $name, 10);

        return $this->redirectToRoute('gateway_supervisor');
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

        //$r = $process->getOutput();
        $r = $this->getProcesses();

        return $this->redirectToRoute('gateway');
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
