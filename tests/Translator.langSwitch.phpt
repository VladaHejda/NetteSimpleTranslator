<?php

use Tester\Assert;
$container = require __DIR__ . '/bootstrap.application.php';

require __DIR__.'/storage/language.php';

use \NetteSimpleTranslator\Translator as Tr;
$trans = new Tr('en', new LanguageStorage, $container->getService('application'));

$trans->setCurrentLanguage('cz');

Assert::equal('Ahoj světe.', $trans->translate('Hello world.'));

$trans->translate('Goodbye home.');
$trans->setCurrentLanguage('de');

Assert::equal('Hallo Welt.', $trans->translate('Hello world.'));
