<?php

namespace DataDictionaryBundle\Tools\Export;

use Doctrine\ORM\Tools\Export\ExportException;

/**
 * Class used for converting your mapping information between the
 * supported formats: json
 *
 * @author  Miguel Herrera <migueljherrera@gmail.com>
 */
class ClassMetadataExporter
{
    /**
     * @var array
     */
    private static $_exporterDrivers = array(
        'json' => 'DataDictionaryBundle\Tools\Export\Driver\JsonExporter',
    );

    /**
     * Registers a new exporter driver class under a specified name.
     *
     * @param string $name
     * @param string $class
     *
     * @return void
     */
    public static function registerExportDriver($name, $class)
    {
        self::$_exporterDrivers[$name] = $class;
    }

    /**
     * Gets an exporter driver instance.
     *
     * @param string      $type The type to get (yml, xml, etc.).
     * @param string|null $dest The directory where the exporter will export to.
     *
     * @return Driver\AbstractExporter
     *
     * @throws ExportException
     */
    public function getExporter($type, $dest = null)
    {
        if ( ! isset(self::$_exporterDrivers[$type])) {
            throw ExportException::invalidExporterDriverType($type);
        }

        $class = self::$_exporterDrivers[$type];

        return new $class($dest);
    }
}
