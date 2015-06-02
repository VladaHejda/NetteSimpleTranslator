<?php

namespace NetteSimpleTranslator;

interface ITranslatorStorage
{

    /**
     * Return translated string.
     * For nonexistent variant return lower variant.
     * Return NULL if translation does not exist.
     * @param string $original
     * @param string $language
     * @param int $variant
     * @param string $namespace
     * @return string|null
     */
    function getTranslation($original, $language, $variant = 0, $namespace = null);

}
