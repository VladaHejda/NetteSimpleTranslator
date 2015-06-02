<?php

namespace NetteSimpleTranslator;

use Nette;
use Nette\Application\Application;

/**
 * Translator implementation.
 *
 * @author Vladislav Hejda
 *
 * @property string $namespace
 * @property string $currentLanguage
 * @property string $defaultLanguage
 * @property array $availableLanguages
 * @property string $languageParameter
 * @property-read bool $currentLangDefault
 */
class Translator extends Nette\Object implements Nette\Localization\ITranslator
{

	/** @var string plural-form meta */
	public static $defaultPluralForms = 'nplurals=1; plural=0;';

	/* @var string */
	private $namespace;

	/** @var string */
	private $defaultLanguage;

	/** @var string */
	private $language;

	/** @var array */
	private $availableLanguages = array();

	/** @var string */
	private $languageParameter = array();

	/** @var ITranslatorStorage */
	private $translatorStorage;

	/** @var Application */
	private $application;


	/**
	 * @param string  $defaultLanguage
	 * @param ITranslatorStorage $translatorStorage
	 */
	public function __construct($defaultLanguage, ITranslatorStorage $translatorStorage, Application $application)
	{
		$this->setDefaultLanguage($defaultLanguage);
		$this->translatorStorage = $translatorStorage;
		$this->application = $application;
	}


	/**
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}


	/**
	 * @return string
	 */
	public function getCurrentLanguage()
	{
		if ($this->language) {
			return $this->language;
		}
		if ($this->languageParameter) {
			$presenter = $this->application->presenter;
			if (isset($presenter->{$this->languageParameter})) {
				$this->setCurrentLanguage($presenter->{$this->languageParameter});
				return $this->language;
			}
		}
		return $this->language = $this->defaultLanguage;
	}


	/**
	 * @return string
	 */
	public function getDefaultLanguage()
	{
		return $this->defaultLanguage;
	}


	/**
	 * @return bool
	 */
	public function isCurrentLangDefault()
	{
		return $this->getCurrentLanguage() === $this->defaultLanguage;
	}


	/**
	 * @return array
	 */
	public function getAvailableLanguages()
	{
		return $this->availableLanguages ? array_keys($this->availableLanguages) : null;
	}


	/**
	 * @param string $language
	 * @return array (nplurals, plural)
	 */
	public function getVariantsCount($language = null)
	{
		list($nplurals) = $this->evalPluralForms(1, $language);
		return $nplurals;
	}


	/**
	 * @param int $count
	 * @param string $language
	 * @return int
	 */
	public function getVariant($count, $language = null)
	{
		list(, $plural) = $this->evalPluralForms($count, $language);
		return $plural;
	}


	/**
	 * @return string
	 */
	public function getLanguageParameter()
	{
		return $this->languageParameter;
	}


	/**
	 * @param string $switchLanguage
	 * @return string
	 */
	public function getPresenterLink($switchLanguage)
	{
		if (!$this->languageParameter) {
			return null;
		}
		return $this->application->presenter->link('this', array($this->languageParameter => $switchLanguage));
	}


	/**
	 * @param string $namespace
	 * @return self
	 * @throws TranslatorException on invalid namespace
	 */
	public function setNamespace($namespace)
	{
		if (!is_string($namespace) || empty($namespace)) {
			throw new TranslatorException('Namespace must be nonempty string.');
		}

		$this->namespace = $namespace;
		return $this;
	}



	/**
	 * Set current language.
	 * @param string $language
	 * @return self
	 * @throws TranslatorException
	 */
	public function setCurrentLanguage($language)
	{
		if (!is_string($language) || empty($language)) {
			throw new TranslatorException('Language must be nonempty string.');
		}
		if ($this->language === $language) {
			return $this;
		}
		if ($this->availableLanguages && !isset($this->availableLanguages[$language])) {
			throw new TranslatorException("Language $language is not available.");
		}

		$this->language = $language;
		return $this;
	}



	/**
	 * Set default language.
	 * @param string $language
	 * @return self
	 * @throws TranslatorException
	 */
	public function setDefaultLanguage($language)
	{
		if (!is_string($language) || empty($language)) {
			throw new TranslatorException('Language must be nonempty string.');
		}
		if ($this->defaultLanguage === $language) {
			return $this;
		}
		if ($this->availableLanguages && !isset($this->availableLanguages[$language])) {
			throw new TranslatorException("Language $language is not available.");
		}

		$this->defaultLanguage = $language;
		return $this;
	}



	/**
	 * Give array with language name associated with plural forms meta such as:
	 * nplurals=3; plural=((n==1) ? 0 : (n>=2 && n<=4 ? 1 : 2));
	 * @param array
	 * @return self
	 * @throws TranslatorException
	 */
	public function setAvailableLanguages(array $languages)
	{
		if (!is_array($languages) || empty($languages)) {
			throw new TranslatorException("Available languages must be nonempty array.");
		}

		foreach ($languages as $language => $pluralForms) {
			if (!is_string($language)) {
				$language = $pluralForms;
				$pluralForms = self::$defaultPluralForms;
			}
			$this->availableLanguages[$language] = $pluralForms;
		}

		if ($this->language && !isset($this->availableLanguages[$this->language])) {
			throw new TranslatorException("Set language $this->language is not available.");
		}
		if (!isset($this->availableLanguages[$this->defaultLanguage])) {
			throw new TranslatorException("Default language $this->defaultLanguage is not available.");
		}

		return $this;
	}


	/**
	 * @param string $paramName
	 */
	public function setLanguageParameter($paramName)
	{
		$this->languageParameter = $paramName;
	}



	/**
	 * Translates string.
	 * Give original string or array of its original variants.
	 * Rest of arguments are handed to sprintf() function.
	 * @param string|array $string
	 * @param int $count
	 * @return string
	 * @throws TranslatorException
	 */
	public function translate($string, $count = 1)
	{
		$hasVariants = FALSE;
		if (is_array($string)) {
			$hasVariants = TRUE;
			$stringVariants = array_map('trim', array_values($string));
			$string = trim((string) $string[0]);
		} else {
			$string = trim((string) $string);
			$plural = 0;
			if (is_array($count)) {
				$args =  $count;

			} elseif (func_num_args() > 2) {
				$args = func_get_args();
				unset($args[0]);
				$args = array_values($args);

			} else {
				$args = array($count);
			}
		}

		if ($hasVariants) {
			if (is_array($count)) {
				$args = $count;
			} elseif (($argc = func_num_args()) > 2) {
				$args = func_get_args();
				unset($args[0]);
				$args = array_values($args);
			} if (isset($args)) {
				unset($count);
				foreach ($args as $arg) {
					if (is_numeric($arg)) {
						$count = (int) $arg;
						break;
					}
				}
				if (!isset($count)) {
					$count = 1;
				}
			}
			else {
				if (is_numeric($count)) {
					$count = (int) $count;
				} else {
					$count = 1;
				}
				$args = array($count);
			}

			$plural = $this->getVariant($count);
		}

		$language = $this->getCurrentLanguage();

		if ($language === $this->defaultLanguage) {
			if ($hasVariants) {
				if (isset($stringVariants[$plural])) {
					$translated = $stringVariants[$plural];
				} else {
					$translated = end($stringVariants);
				}
			}
			else {
				$translated = $string;
			}
		}

		else {
			$translated = $this->translatorStorage->getTranslation($string, $language, $plural, $this->namespace);
			if (!is_string($translated) && !is_null($translated)) {
				throw new TranslatorException('ITranslatorStorage::getTranslation() must return string, '.gettype($translated).' returned.');
			}

			if (!$translated) {
				if ($hasVariants) {
					if (isset($stringVariants[$plural])) {
						$translated = $stringVariants[$plural];
					} else {
						$translated = end($stringVariants);
					}
				} else {
					$translated = $string;
				}
			}
		}

		if (FALSE !== strpos($translated, '%')) {
			// preserve form messages substitutes
			$tmp = str_replace(array('%label', '%name', '%value'), array('#label', '#name', '#value'), $translated);
			if (FALSE !== strpos($tmp, '%')) {
				$translated = vsprintf($tmp, $args);
				$translated = str_replace(array('#label', '#name', '#value'), array('%label', '%name', '%value'), $translated);
			}
		}

		return $translated;
	}



	private function evalPluralForms($count = 1, $language = null)
	{
		$language = $language ?: $this->getCurrentLanguage();
		$pluralForms = isset($this->availableLanguages[$language]) ? $this->availableLanguages[$language] : self::$defaultPluralForms;
		if (!$pluralForms) {
			throw new TranslatorException("Empty plural-form meta for language $language.");
		}

		$eval = preg_replace('/([a-z]+)/', '$$1', "n=$count;$pluralForms");
		eval($eval);

		if (!isset($nplurals)) {
			throw new TranslatorException("Cannot resolve nplurals form count for $language. Check plural-form meta $pluralForms.");
		}
		if (!isset($plural)) {
			throw new TranslatorException("Cannot resolve plural form for $language. Check plural-form meta $pluralForms.");
		}
		if (($plural +1) > $nplurals) {
			throw new TranslatorException(
				"Plural-form parse error for $language. Plural form cannot exceed ".($nplurals-1)
			  . " regarding to nplural=$nplurals, but $plural returned. Check plural-form meta $pluralForms.");
		}

		return array($nplurals, $plural);
	}

}
