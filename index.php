<?php

$loader = require __DIR__.'/vendor/autoload.php';
require __DIR__ . '/app/AppKernel.php';

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Annotations\AnnotationRegistry;

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

$kernel = new Necktie\Gateway\AppKernel('dev', true);

$request  = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
