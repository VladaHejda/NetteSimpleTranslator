<?php

class NamespaceStorage implements \NetteSimpleTranslator\ITranslatorStorage
{

	private $translations = array(
		'first' => array(
			'Hello world.' => 'Ahoj světe.',
		),
		'second' => array(
			'My name is %s.' => 'Jmenuji se %s.',
		),
	);


	public function getTranslation($original, $l, $v = 0, $ns = null)
	{
		if (!isset($this->translations[$ns][$original])) {
			return null;
		}
		return $this->translations[$ns][$original];
	}

}
