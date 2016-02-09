<?php


namespace Necktie\Bundle\GatewayBundle;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;


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


    public function sendToNecktie($data){
        try{
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

            $this->count = 0;
        }catch( \Exception $ex ){
            sleep(rand(10, 100));

            if($this->count < 100){
                $this->count++;
                $this->sendToNecktie($data);
                //todo error
            }

            $this->count = 0;
        }
    }

}