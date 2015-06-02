<?php

use Tester\Assert;
$container = require __DIR__ . '/bootstrap.application.php';

require __DIR__.'/storage/dummy.php';

$trans = new \NetteSimpleTranslator\Translator('en', new DummyStorage, $container->getService('application'));

Assert::equal('hello', $trans->translate('hello'));
