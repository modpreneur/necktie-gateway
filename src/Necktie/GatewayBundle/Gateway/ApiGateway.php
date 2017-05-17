<?php

namespace Necktie\GatewayBundle\Gateway;

use Elasticsearch\Common\Exceptions\ClientErrorResponseException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Necktie\GatewayBundle\Exceptions\URLException;
use Necktie\GatewayBundle\Services\ApiCaller;
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

    /** @var  ApiCaller */
    protected $apiCaller;


    /**
     * ApiGateway constructor.
     *
     * @param ApiCaller $apiCaller
     */
    public function __construct(ApiCaller $apiCaller)
    {
        $this->validator = (new ValidatorBuilder())->getValidator();
        $this->apiCaller = $apiCaller;
    }


    /**
     * @param $method
     * @param string $url
     * @param array $header
     * @param string $body
     *
     * @param null $tag
     * @param array $data
     *
     * @return array
     * @throws \RuntimeException
     * @throws URLException
     */
    public function request($method, $url, array $header = [], $body = '', $tag = null, array $data = []): array
    {
        $response = null;

        if (($val = $this->validateUrl($url))) {
            //throw new URLException($val);
        }

        $datetime = (new \DateTime())->format(DATE_W3C);

        try {
            $response = $this
               ->apiCaller
               ->request($method, $url, ['headers' => $header, 'body' => json_encode($body)], $tag);
        } catch (ClientException $ex) {
            echo 'API[ERROR][' . $datetime .'][' . $method . '] ('.$url.'):' . $ex->getMessage() . PHP_EOL;

            return [
                'status'   => 'error',
                'url'      => $url,
                'response' => (string)($ex->getResponse()->getBody()),
                'error'    => $ex->getMessage() . ' ' . $ex->getFile() . ':' . $ex->getLine(),
                'data'     => $data,
            ];
        }

        echo 'API[OK][' . $datetime .'][' . $method . '] ('.$url.')' . PHP_EOL;

        return [
            'status'   => 'ok',
            'url'      => $url,
            'response' => (string)$response->getBody(), // todo here
            'tag'      => $tag,
            'data'     => $data,
        ];
    }


    /**
     * @param string $url
     * @return string
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected function validateUrl($url): string
    {
        return (string)$this->validator->validate($url, new Url());
    }
}