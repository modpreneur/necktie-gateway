<?php

namespace Necktie\Bundle\GatewayBundle\Command;


use djchen\OAuth1;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class TestCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $oauth = new OAuth1(array(
            'consumerKey'     => 'Az0BmiXcN9XbUsHwaR14YeOX',
            'consumerSecret'  => 'KOCR7OzaiAzmXEHxXdgro1e8Ud0pIYFGSf626uFG',
            'requestTokenUrl' => 'https://auth.aweber.com/1.0/oauth/request_token',
            'accessTokenUrl'  => 'https://auth.aweber.com/1.0/oauth/access_token',
        ));

        //$r = $oauth->requestToken('http://necktie.docker:9080/oauth');

        //var_dump($r);

        $result = $oauth->accessToken('AqtTtARriAqeheK8ZS091fNW', 'odJ95cZyyttlLqGdXoFp12qGBz7fFyz0yoMy1bI6', 'zym5e8');

        var_dump($result);

        $r = $oauth->get('https://api.aweber.com/1.0/accounts/c7d60e66');
        var_dump($r);


        exit;



    }
}