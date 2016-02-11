<?php


namespace Necktie\Bundle\GatewayBundle;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class NecktieSender
 * @package Necktie\Bundle\GatewayBundle
 */
class NecktieSender
{

    /** @var  string */
    protected $necktieUrl;

    /** @var  string */
    protected $apiUrl;

    /** @var int  */
    protected $count = 0;


    /**
     * NecktieSender constructor.
     * @param $necktieUrl
     * @param $apiUrl
     */
    public function __construct($necktieUrl, $apiUrl)
    {
        $this->necktieUrl = $necktieUrl;
        $this->apiUrl     = $apiUrl;
    }


    public function sendToNecktie($data, OutputInterface $output){
        try{
            $output->writeln('[' . (new \DateTime())->format(\DateTime::W3C) . '] <info> Send to server. </info> ');
            $client   = new Client();
            $request  = new Request(
                'POST',
                $this->necktieUrl . $this->apiUrl,
                [
                    "accept"       => "application/json",
                    "content-Type" => "application/json"
                ],
                json_encode($data)
            );
            $response = $client->send($request);
            $output->writeln('[' . (new \DateTime())->format(\DateTime::W3C) . '] <info> Response from server: </info> ' . (string)$response->getBody());

            $this->count = 0;
        }catch( \Exception $ex ){
            $output->writeln('[' . (new \DateTime())->format(\DateTime::W3C) . '] <error> ' .$ex->getMessage() . '</error>.');
            sleep(rand(1, 60));
            if($this->count < rand(5, 10)){
                $this->count++;
                $this->sendToNecktie($data, $output);
                //todo error
            }

            $this->count = 0;
        }
    }

}