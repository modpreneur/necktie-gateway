#!/usr/bin/env php
<?php

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Debug\Debug;

// if you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

set_time_limit(0);

/**
 * @var Composer\Autoload\ClassLoader $loader
 */
$loader = require __DIR__.'/vendor/autoload.php';

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
\Doctrine\DBAL\Types\Type::addType('uuid', 'Ramsey\Uuid\Doctrine\UuidType');


$input = new ArgvInput();
$env = $input->getParameterOption(array('--env', '-e'), getenv('SYMFONY_ENV') ?: 'dev');
$debug = getenv('SYMFONY_DEBUG') !== '0' && !$input->hasParameterOption(array('--no-debug', '')) && $env !== 'prod';

if ($debug) {
    Debug::enable();
}

require_once __DIR__.'/app/AppKernel.php';
$kernel = new \Necktie\Gateway\AppKernel($env, $debug);
$application = new Application($kernel);

$application->run($input);

$entityManager = $application->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('uuid', 'uuid');