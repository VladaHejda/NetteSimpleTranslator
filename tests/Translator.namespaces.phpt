<?php

use Tester\Assert;
$container = require __DIR__ . '/bootstrap.application.php';

require __DIR__.'/storage/namespace.php';

use \NetteSimpleTranslator\Translator as Tr;
$trans = new Tr('en', new NamespaceStorage, $container->getService('application'));

$trans->setCurrentLanguage('cz')
	->setNamespace('first');

Assert::equal('Ahoj světe.', $trans->translate('Hello world.'));
$trans->translate('new string');

$trans->setNamespace('second');
Assert::equal('Hello world.', $trans->translate('Hello world.'));
Assert::equal('Jmenuji se George.', $trans->translate('My name is %s.', 'George'));
