<?php

require __DIR__.'/bootstrap.php';

use Tester\Assert;
require __DIR__.'/storage/dummy.php';

$configurator = new Nette\Configurator;
$configurator->setTempDirectory(__DIR__ . '/temp');
$configurator->addConfig(__DIR__.'/config/minimum.neon');
$container = $configurator->createContainer();

Assert::type('NetteSimpleTranslator\Translator', $container->getService('translator'));
