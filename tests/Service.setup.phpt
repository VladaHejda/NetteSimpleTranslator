<?php

require __DIR__.'/bootstrap.php';

use Tester\Assert;
require __DIR__.'/storage/dummy.php';

$configurator = new Nette\Configurator;
$configurator->setTempDirectory(__DIR__ . '/temp');
$configurator->addConfig(__DIR__.'/config/setup.neon');
$container = $configurator->createContainer();

/** @var $trans NetteSimpleTranslator\Translator */
$trans = $container->getService('translator');


Assert::equal(array('en', 'cz', 'de'), $trans->getAvailableLanguages());
Assert::equal('front', $trans->getNamespace());
