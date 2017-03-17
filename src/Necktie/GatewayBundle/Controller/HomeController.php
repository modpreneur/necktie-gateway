<?php

namespace Necktie\GatewayBundle\Controller;

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

/**
 * Class HomeController
 * @package Necktie\GatewayBundle\Controller
 */
class HomeController extends Controller
{
    protected $supervisor;


    /**
     * HomeController constructor.
     *
     */
    public function __construct()
    {
        $this->supervisor = new Supervisor($this->getConnector());
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
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
            'supervisorCommands' => $this->getProcesses(),
            'messages'   => $messages,
            'systemLogs' => $systemLogs,
            'rabbit'     => $rabbit,
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

        return $this->redirectToRoute('gateway');
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

        foreach ($this->supervisor->tailProcessStdoutLog($group . ':' . $name, 0, -1) as $item) {
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
        return $this->redirectToRoute('gateway');
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

        return $this->redirectToRoute('gateway');
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
}
