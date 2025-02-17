<?php

declare (strict_types=1);
namespace Rector\Core\Configuration;

use Rector\Core\Configuration\Parameter\SimpleParameterProvider;
/**
 * Rector native configuration provider, to keep deprecated options hidden,
 * but also provide configuration that custom rules can check
 */
final class RectorConfigProvider
{
    public function shouldImportNames() : bool
    {
        return SimpleParameterProvider::provideBoolParameter(\Rector\Core\Configuration\Option::AUTO_IMPORT_NAMES);
    }
    public function shouldRemoveUnusedImports() : bool
    {
        return SimpleParameterProvider::provideBoolParameter(\Rector\Core\Configuration\Option::REMOVE_UNUSED_IMPORTS);
    }
    /**
     * @api symfony
     */
    public function getSymfonyContainerPhp() : string
    {
        return SimpleParameterProvider::provideStringParameter(\Rector\Core\Configuration\Option::SYMFONY_CONTAINER_PHP_PATH_PARAMETER);
    }
    /**
     * @api symfony
     */
    public function getSymfonyContainerXml() : string
    {
        return SimpleParameterProvider::provideStringParameter(\Rector\Core\Configuration\Option::SYMFONY_CONTAINER_XML_PATH_PARAMETER);
    }
    public function getIndentChar() : string
    {
        return SimpleParameterProvider::provideStringParameter(\Rector\Core\Configuration\Option::INDENT_CHAR, ' ');
    }
    public function getIndentSize() : int
    {
        return SimpleParameterProvider::provideIntParameter(\Rector\Core\Configuration\Option::INDENT_SIZE);
    }
}
