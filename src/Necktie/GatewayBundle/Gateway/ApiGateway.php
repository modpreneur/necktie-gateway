<?php

namespace Necktie\GatewayBundle\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Necktie\GatewayBundle\Exceptions\URLException;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;

/**
 * Class ApiGateway
 * @package App\Gateway
 */
class ApiGateway
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /** @var ClientFactoryInterface */
    protected $clientFactory;


    /**
     * ApiGateway constructor.
     * @param ClientFactoryInterface $clientFactory
     */
    public function __construct(ClientFactoryInterface $clientFactory)
    {
        $this->validator = (new ValidatorBuilder())->getValidator();
        $this->clientFactory = $clientFactory;
    }


    /**
     * @return Client
     */
    protected function getClient()
    {
        return $this->clientFactory->createClient();
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
     * @throws URLException
     */
    public function request($method, $url, array $header = [], $body = "", $tag = null, array $data = [])
    {
        if (($val = $this->validateUrl($url))) {
            throw new URLException($val);
        }

        $request = new Request($method, $url, $header, json_encode($body));
        $client  = $this->getClient();

        try {

            $response = $client->send($request, [
                'verify' => false,
            ]);

        } catch (\Exception $ex) {

            echo 'API[ERROR] ('.$url.'):' . $ex->getMessage() . PHP_EOL;

            return [
                'status'   => 'error',
                'url'      => $url,
                'response' => $ex->getMessage(),
                'error'    => $ex->getMessage(),
                'data'     => $data,
            ];
        }

        echo 'API[OK] ('.$url.')' . PHP_EOL;

        return [
            'status'   => 'ok',
            'url'      => $url,
            'response' => (string)$response->getBody()->getContents(), // todo here
            'tag'      => $tag,
            'data'     => $data,
        ];
    }


    /**
     * @param string $url
     * @return string
     */
    protected function validateUrl($url)
    {
        return (string)$this->validator->validate($url, new Url());
    }

}