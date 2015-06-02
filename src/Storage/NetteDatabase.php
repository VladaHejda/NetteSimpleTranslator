<?php

namespace NetteSimpleTranslator\Storage;

use Nette\Database\Context;

class NetteDatabase implements \NetteSimpleTranslator\ITranslatorStorage
{

	/** @var Context */
	private $database;

	/** @var string */
	private $defaultTable;

	/** @var string */
	private $translationTable;



	/**
	 * @param string $defaultTableName name of table with original texts
	 * @param string $translationTableName name of table with translated texts
	 * @param Context $database
	 */
	public function __construct($defaultTableName, $translationTableName, Context $database)
	{
		$this->database = $database;
		$this->defaultTable = $defaultTableName;
		$this->translationTable = $translationTableName;
	}



	public function getTranslation($original, $language, $variant = 0, $namespace = null)
	{
		$selection = $this->database->table($this->translationTable)
			->where('language', $language)
			->where('variant <=', $variant)
			->where($this->defaultTable . '.text', $original)
			->order('variant DESC');

		if ($namespace) {
			$selection->where($this->defaultTable . '.ns', $namespace);
		}

		return $selection->fetch()->translation;
	}

}
