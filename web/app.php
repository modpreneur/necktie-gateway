<?php

$loader = require __DIR__.'/../vendor/autoload.php';
//require __DIR__.'/../app/AppKernel.php';

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpFoundation\Request;


AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

$kernel = new Necktie\Gateway\AppKernel('dev', true);

$request  = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
