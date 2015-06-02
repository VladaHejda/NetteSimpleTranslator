<?php

require __DIR__ . '/bootstrap.php';

// todo mazat automaticky cache?

$configurator = new Nette\Configurator;
$configurator->setTempDirectory(__DIR__ . '/temp');
return $configurator->createContainer();
