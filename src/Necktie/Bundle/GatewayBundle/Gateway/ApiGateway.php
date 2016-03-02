<?php

namespace Necktie\Bundle\GatewayBundle\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Class ApiGateway
 * @package App\Gateway
 */
class ApiGateway
{
    protected $allowedMethod = [ 'GET', 'PUT', 'POST', 'DELETE' ];

    /**
     * @var ValidatorInterface
     */
    protected $validator;


    /**
     * ApiGateway constructor.
     */
    public function __construct()
    {
        $this->validator = ( new ValidatorBuilder() )->getValidator();
    }


    /**
     * @param $method
     * @param string $url
     * @param array $header
     * @param string $body
     *
     * @param null $tag
     * @param array $data
     * @return string
     * @throws \Exception
     */
    public function request($method, $url, array $header = [], $body = "", $tag = null, array $data = []){

        $this->checkMethod($method);
        $header = $this->validateHeader($header);

        if( ($val =  $this->validateUrl($url)) ){
            throw new \Exception($val);
        }


        $request = new Request($method, $url, $header, json_encode($body));
        $client = new Client();

        try{

            $response = $client->send($request, [
                'verify' => false
            ]);


        }catch(\Exception $ex){
            return [
                'status'  => 'error',
                'url'     => $url,
                'message' => $ex->getMessage(),
                'error'   => $ex->getMessage(),
                'data'    => $data,
            ];
        }

        return [
            'status'  => 'ok',
            'url'     => $url,
            'body'    => ((string) $response->getBody()),
            'tag'     => $tag,
            'data'    => $data,
        ];
    }


    /**
     * @param string $method
     * @throws \Exception
     */
    protected function checkMethod($method){
        if( !in_array(strtoupper($method), $this->allowedMethod) ){
            throw new \Exception('Allowed methods are: ' . join(', ', $this->allowedMethod));
        }
    }


    /**
     * @param array $header
     * @return array
     */
    protected function validateHeader(array $header): array {
        $optionsResolver = new OptionsResolver;
        $optionsResolver->setDefaults([
            "accept"       => "application/json",
            "content-Type" => "application/json"
        ]);

        //$header = $optionsResolver->resolve($header);

        return $header;
    }


    /**
     * @param string $url
     * @return string
     */
    protected function validateUrl($url){
        return (string)  $this->validator->validate(
            $url,
            new Url()
        );
    }

}