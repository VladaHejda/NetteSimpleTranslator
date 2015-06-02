<?php

namespace NetteSimpleTranslator\Storage;

use Nette\Database\Context;

class NetteDatabase implements \NetteSimpleTranslator\ITranslatorStorage
{

	/** @var Context */
	private $database;

	/** @var string */
	private $translationTable;



	/**
	 * @param string $translationTable name of table with translated texts
	 * @param Context $database
	 */
	public function __construct($translationTable, Context $database)
	{
		$this->database = $database;
		$this->translationTable = $translationTable;
	}



	public function getTranslation($original, $language, $variant = 0, $namespace = null)
	{
		$selection = $this->database->table($this->translationTable)
			->where('lang', $language)
			->where('variant <=', $variant)
			->where('text.text', $original)
			->order('variant DESC');

		if ($namespace) {
			$selection->where('text.ns', $namespace);
		}

		return ($row = $selection->fetch()) ? $row->translation : null;
	}

}
