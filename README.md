NetteSimpleTranslator
=====================

NetteSimpleTranslator is tool to simply translate your web apps based on [Nette Framework](http://nette.org/en/).

You have an app published in certain language, e.g. english. All you need to do is fullfill your database with
translated texts and easily mark them in your app to translate, see below. 


Installation
------------

- Download from Github: <https://github.com/VladaHejda/NetteSimpleTranslator>
- or better use [Composer](http://getcomposer.org/doc/00-intro.md#declaring-dependencies):

```json
{
	"require": {
		"vladahejda/nettesimpletranslator": "~1.0"
	}
}
```

Then load classes via autoloader ([composer autoloading](http://getcomposer.org/doc/01-basic-usage.md#autoloading)
or Nette RobotLoader).


Usage
-----

To launch the translator follow these steps:


### 1. prepare storage

- **Using Nette Database**

Execute SQL script in [`src/Storage/NetteDatabase.createTable.sql`](/src/Storage/NetteDatabase.createTable.sql)
(or its [namespaced version]((/src/Storage/NetteDatabase.createTable.namespaced.sql)),
see [using namespaces](#using-namespaces)) at your database.

Open your configuration file and add service:
```
services:
	translatorStorage: NetteSimpleTranslator\Storage\NetteDatabase(localization_text, localization)
```

*You can rename tables in SQL script (use the same names in config file).*


- **I want to save translations elsewhere**

Look at the interface [`NetteSimpleTranslator\ITranslatorStorage`](/src/ITranslatorStorage.php) and implement it to write your own storage.

Then add storage into your configuration file as a service.


### 2. add NetteSimpleTranslator service

Into your config file add service `NetteSimpleTranslator\Translator` and define the default language
(it is language which in your web is written basically):
```
services:
	translator: NetteSimpleTranslator\Translator(en)
```


### 3. set up your BasePresenter

Inject NetteSimpleTranslator, set current language and bring translator into template and forms:
```php
class BasePresenter extends \Nette\Application\UI\Presenter
{

	/** @var string @persistent */
	public $language = 'en';
	
	/** @var \NetteSimpleTranslator\Translator */
	protected $translator;
	
	
	public function injectTranslator(\NetteSimpleTranslator\Translator $translator)
	{
		$this->translator = $translator;
	}
	
	
	public function startup()
	{
		parent::startup();
		$this->translator->setCurrentLanguage($this->language);
	}
	
	
	protected function createTemplate()
	{
		$template = parent::createTemplate();
		$template->setTranslator($this->translator);
		return $template;
	}
	

	// to have translated even forms add this method too
	protected function createComponent($name)
	{
		$component = parent::createComponent($name);
		if ($component instanceof \Nette\Forms\Form) {
			$component->setTranslator($this->translator);
		}
		return $component;
	}
	
}
```


### 4. mark texts for translation

In presenters call `$this->translator->translate('text to translate')`, in latte use underscore macro
`{_ 'text to translate'}`

Done. Make links to switch your `$language` persistent parameter and see the translations.


Advanced
--------

If you want to use your translator fully, there is some more stuff you would do.


### Say translator which languages you use

Call function `$translator->setAvailableLanguages()` and give the array of languages that your web is available in.
Then set the name of presenter language persistent parameter (`$translator->setLanguageParameter()`).

Better way of this is setting it in config:
```
	translator:
		class: NetteSimpleTranslator\Translator(en)
		setup:
			- setAvailableLanguages([en, de, fr])
			- setLanguageParameter(language)
```

Translator will now recognize the current language itself.


### Use skills of `sprintf`

If you give more arguments to translate method, it will be handed to php function
[sprintf](http://php.net/manual/en/function.sprintf.php).

That means that `$translator->translate('Call me %s.', 'Johan')` results in "Call me Johan", whereas
"Johan" will not be translated.

It can be used in latte too.


### Translate plurals (1 apple → 2 apples)

You can say what plural-form each language uses via `setAvailableLanguages`, this way:
```
	setup:
		- setAvailableLanguages([
			en: "nplurals=2; plural=(n==1) ? 0 : 1;",
			cz: "nplurals=3; plural=((n==1) ? 0 : (n>=2 && n<=4 ? 1 : 2));",
		])
```
(to understand this see [plural forms](https://github.com/translate/l10n-guide/blob/master/docs/l10n/pluralforms.rst#plural-forms))

Then you can add even translations in plural. All you need is to use the column `variant` in translations database
(in case you use the [NetteDatabase](/src/Storage/NetteDatabase.createTable.sql) storage).

More you need to do is to give plural variants of the default language to the translator, in array. And the number.
Example: `$translator->translate( array( 'There is %d apple', 'There is %d apples' ), 3 )` or in latte:
`{_ ['There is %d apple', 'There is %d apples'], 3}`.


### Using namespaces

When there is huge amount of texts at your web, it would be good to sort them somehow. Just give the namespace
to the translator dependent for example on [module](http://doc.nette.org/en/presenters#toc-modules).
`$translator->setNamespace('products')`


And now, enjoy.


Under *New BSD License*
