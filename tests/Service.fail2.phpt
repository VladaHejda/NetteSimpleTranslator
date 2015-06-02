<?php

require __DIR__.'/bootstrap.php';

use Tester\Assert;
require __DIR__.'/storage/dummy.php';

$configurator = new Nette\Configurator;
$configurator->setTempDirectory(__DIR__ . '/temp');
$configurator->addConfig(__DIR__.'/config/fail.noStorage.neon');

Assert::exception(function() use($configurator){
	$configurator->createContainer();
}, 'Nette\InvalidStateException');
