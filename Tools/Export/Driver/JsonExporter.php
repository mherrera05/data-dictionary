<?php

namespace DataDictionaryBundle\Tools\Export\Driver;

use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\Export\Driver\AbstractExporter;

/**
 * ClassMetadata exporter for Doctrine Json mapping files.
 *
 * @author  Miguel Herrera <migueljherrera@gmail.com>
 */
class JsonExporter extends AbstractExporter
{
    /**
     * @var string
     */
    protected $_extension = '.dcm.json';

    /**
     * {@inheritdoc}
     */
    public function exportClassMetadata(ClassMetadataInfo $metadata)
    {
        $array = array();
        $array['table'] = $metadata->table['name'];

        if (isset($metadata->table['schema'])) {
            $array['schema'] = $metadata->table['schema'];
        }

        $fieldMappings = $metadata->fieldMappings;
        $ids = array();
        $fields = array();

        foreach ($fieldMappings as $name => $fieldMapping) {
            $nameAsDatabase = $fieldMapping['columnName'];
            $fieldMapping['column'] = $fieldMapping['columnName'];
            unset($fieldMapping['columnName'], $fieldMapping['fieldName']);
            unset($fieldMapping['column']);
            $fields[$nameAsDatabase] = $fieldMapping;
        }

        if ($fields) {
            if ( ! isset($array['fields'])) {
                $array['fields'] = array();
            }
            $array['fields'] = array_merge($array['fields'], $fields);
        }

        foreach ($metadata->associationMappings as $name => $associationMapping) {

            $associationMappingArray = array(
                'targetEntity' => $associationMapping['targetEntity'],
            );

            if (isset($mapping['id']) && $mapping['id'] === true) {
                $array['id'][$name]['associationKey'] = true;
            }

            if ($associationMapping['type'] & ClassMetadataInfo::TO_ONE) {
                $joinColumns = $associationMapping['joinColumns'];
                $newJoinColumns = array();
                $nameJoinColumnAsDatabase = '';

                foreach ($joinColumns as $joinColumn) {
                    $newJoinColumns['referencedColumnName'] = $joinColumn['referencedColumnName'];
                    
                    if(count($newJoinColumns)==1){
                        $nameJoinColumnAsDatabase = $joinColumn['name']; 
                    }
                }

                $oneToOneMappingArray = $newJoinColumns;
                $associationMappingArray = array_merge($associationMappingArray, $oneToOneMappingArray);

                if ($associationMapping['type'] & ClassMetadataInfo::ONE_TO_ONE) {
                    $array['oneToOne'][$nameJoinColumnAsDatabase] = $associationMappingArray;
                } else {
                    $array['manyToOne'][$nameJoinColumnAsDatabase] = $associationMappingArray;
                }
            } 
        }

        return json_encode(array($metadata->name => $array));
    }
}
