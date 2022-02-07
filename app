#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use JimLind\Application;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

$container = new ContainerBuilder();
$yamlLoader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/config'));
$yamlLoader->load('services.yaml');
$container->compile();

$application = $container->get(Application::class);
$application->run();
