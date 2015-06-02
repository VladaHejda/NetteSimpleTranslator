<?php

use Tester\Assert;
$container = require __DIR__ . '/bootstrap.application.php';

require __DIR__.'/storage/dummy.php';

Assert::exception(function() use($container){
	new \NetteSimpleTranslator\Translator('', new DummyStorage, $container->getService('application'));
}, 'NetteSimpleTranslator\TranslatorException');

$trans = new \NetteSimpleTranslator\Translator('en', new DummyStorage, $container->getService('application'));

$trans->setAvailableLanguages(array(
	'en', 'cz'
));

Assert::exception(function() use($trans){
	$trans->setCurrentLanguage('de');
}, 'NetteSimpleTranslator\TranslatorException');

Assert::exception(function() use($trans){
	$trans->setDefaultLanguage('de');
}, 'NetteSimpleTranslator\TranslatorException');

$trans = new \NetteSimpleTranslator\Translator('de', new DummyStorage(), $container->getService('application'));

Assert::exception(function() use($trans){
	$trans->setAvailableLanguages(array(
		'en', 'cz'
	));
}, 'NetteSimpleTranslator\TranslatorException');

$trans = new \NetteSimpleTranslator\Translator('en', new DummyStorage(), $container->getService('application'));

$trans->setCurrentLanguage('de');

Assert::exception(function() use($trans){
	$trans->setAvailableLanguages(array(
		'en', 'cz'
	));
}, 'NetteSimpleTranslator\TranslatorException');
