<?php

namespace Necktie\GatewayBundle\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as R7;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class RabbitReader
 * @package Necktie\GatewayBundle\Services
 */
class RabbitReader
{
    protected $data = array();

    /**
     * @var string
     */
    protected $rabbitUrl;

    /**
     * @var string
     */
    protected $rabbitPort;

    /** @var  string */
    protected $rabbitUser;

    /** @var  string */
    protected $rabbitPassword;


    /**
     * RabbitCollector constructor.
     *
     * @param string $rabbitUrl
     * @param string $rabbitPort
     * @param string $rabbitUser
     * @param string $rabbitPassword
     */
    public function __construct($rabbitUrl, $rabbitPort, $rabbitUser, $rabbitPassword)
    {
        $this->rabbitUrl = $rabbitUrl;
        $this->rabbitPort = $rabbitPort;
        $this->rabbitUser = $rabbitUser;
        $this->rabbitPassword = $rabbitPassword;
    }


    /**
     * @return Client
     */
    private function getClient(): Client
    {
        return new Client(['connect_timeout' => 3]);
    }


    /**
     * Collects data for the given Request and Response.
     *
     * @param Request $request A Request instance
     * @param Response $response A Response instance
     * @param \Exception $exception An Exception instance
     *
     * @throws \LogicException
     */
    public function process()
    {
        $client = $this->getClient();
        $queues = [];
        $error = null;
        $messages = [];

        try {
            $response = $client->get(
                $this->url('queues'),
                $this->getAuth()
            );

            $queues = json_decode((string)$response->getBody(), true);

            foreach ($queues as $queue) {
                $messages[$queue['name']] = $this->getMessages($queue['name']);
            }
        } catch (ConnectException $ex) {
            $error = $ex->getMessage();
        } catch (RequestException $ex) {
            $error = $ex->getMessage();

//            401 = unauthorized = bad credentials
            if ($ex->getResponse()->getStatusCode() === 401) {
                throw new \LogicException(
                    'You have invalid username or password or missing tag \'Management\'for rabbitMQ'
                );
            }
        }

        $this->data = [
            'error' => $error,
            'queues' => $queues,
            'messages' => $messages,
        ];
    }


    /**
     * @return bool
     */
    public function hasError()
    {
        try {
            $this->getClient()->get($this->url(''));
        } catch (ConnectException $exception) {
            return true;
        }

        return false;
    }


    /**
     * @return string
     */
    public function getConnectionError()
    {
        try {
            $this->getClient()->get($this->url(''));
        } catch (ConnectException | RequestException $exception) {
            return $exception->getMessage();
        }

        return '';
    }


    /**
     * @param string $queue
     *
     * @return mixed
     */
    public function getMessages(string $queue)
    {
        $client = $this->getClient();

        $cred = base64_encode($this->rabbitUser . ':' .$this->rabbitPassword);

        $request = new R7(
            'POST',
            $this->url('queues/%2f/' . $queue . '/get'),
            [
                'Accept'       => 'application/json',
                'content-type' => 'application/json',
                'Authorization' => "Basic $$cred"
            ],
            \GuzzleHttp\json_encode(
                [
                    'count'    => 10,
                    'encoding' => 'auto',
                    'requeue'  => true
                ]
            )
        );

        $response = (string)$client->send($request)->getBody();
        return \GuzzleHttp\json_decode($response, true);
    }


    /**
     * @return string
     */
    private function getEndPoint() : string
    {
        return 'http://' . $this->rabbitUrl . ':' . $this->rabbitPort . '/api/';
    }


    /**
     * @param string $path
     *
     * @return string
     */
    private function url(string $path) : string
    {
        return $this->getEndPoint() . $path;
    }


    /**
     * @return array
     */
    private function getAuth()
    {
        return ['auth' => [$this->rabbitUser, $this->rabbitPassword]];
    }


    /**
     * @return mixed
     */
    public function getQueues()
    {
        return $this->data['queues'];
    }


    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->data['error'];
    }


    /**
     * @return mixed
     */
    public function getQueueMessages()
    {
        return $this->data['messages'];
    }

}