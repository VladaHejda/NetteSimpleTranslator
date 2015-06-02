<?php

class LanguageStorage implements \NetteSimpleTranslator\ITranslatorStorage
{

	private $translations = array(
		'cz' => array(
			'Hello world.' => 'Ahoj světe.',
		),
		'de' => array(
			'Hello world.' => 'Hallo Welt.',
		),
	);


	public function getTranslation($original, $language, $v = 0, $n = null)
	{
		if (!isset($this->translations[$language][$original])) {
			return null;
		}
		return $this->translations[$language][$original];
	}

}
