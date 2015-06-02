<?php

class SimpleStorage implements \NetteSimpleTranslator\ITranslatorStorage
{

	private $translations = array(
		'Hello world.' => array('Ahoj světe.'),
		'My name is %s.' => array('Jmenuji se %s.'),
		'here is %d apple' => array('zde je %d jablko', 'zde jsou %d jablka', 'zde je %d jablek'),
		'%d pear' => array('%d hruška', '%d hrušky'),
		'name %s' => array('jméno %s', 'jména %s'),
		'%2$d man screams %1$s.' => array('%1$s křičí %2$d muž.', '%2$d muži křičí "%1$s!".', '%1$s křičí %2$d mužů.'),
	);


	public function getTranslation($original, $l, $variant = 0, $n = null)
	{
		if (!isset($this->translations[$original])) {
			return null;
		}
		return isset($this->translations[$original][$variant])
			? $this->translations[$original][$variant]
			: end($this->translations[$original])
		;
	}

}
