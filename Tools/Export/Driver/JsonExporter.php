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

        /*if ($metadata->isMappedSuperclass) {
            $array['type'] = 'mappedSuperclass';
        } else {
            $array['type'] = 'entity';
        }*/

        $array['table'] = $metadata->table['name'];

        if (isset($metadata->table['schema'])) {
            $array['schema'] = $metadata->table['schema'];
        }

        /*$inheritanceType = $metadata->inheritanceType;

        if ($inheritanceType !== ClassMetadataInfo::INHERITANCE_TYPE_NONE) {
            $array['inheritanceType'] = $this->_getInheritanceTypeString($inheritanceType);
        }

        if ($column = $metadata->discriminatorColumn) {
            $array['discriminatorColumn'] = $column;
        }

        if ($map = $metadata->discriminatorMap) {
            $array['discriminatorMap'] = $map;
        }

        if ($metadata->changeTrackingPolicy !== ClassMetadataInfo::CHANGETRACKING_DEFERRED_IMPLICIT) {
            $array['changeTrackingPolicy'] = $this->_getChangeTrackingPolicyString($metadata->changeTrackingPolicy);
        }

        if (isset($metadata->table['indexes'])) {
            $array['indexes'] = $metadata->table['indexes'];
        }

        if ($metadata->customRepositoryClassName) {
            $array['repositoryClass'] = $metadata->customRepositoryClassName;
        }

        if (isset($metadata->table['uniqueConstraints'])) {
            $array['uniqueConstraints'] = $metadata->table['uniqueConstraints'];
        }*/

        $fieldMappings = $metadata->fieldMappings;

        $ids = array();
        foreach ($fieldMappings as $name => $fieldMapping) {
            $nameAsDatabase = $fieldMapping['columnName'];

            echo $nameAsDatabase;
            $fieldMapping['column'] = $fieldMapping['columnName'];

            unset($fieldMapping['columnName'], $fieldMapping['fieldName']);

            if ($fieldMapping['column'] == $name) {
                unset($fieldMapping['column']);
            }

            /*if (isset($fieldMapping['id']) && $fieldMapping['id']) {
                $ids[$name] = $fieldMapping;
                unset($fieldMappings[$name]);
                continue;
            }*/

            $fieldMappings[$nameAsDatabase] = $fieldMapping;
        }

        if ( ! $metadata->isIdentifierComposite && $idGeneratorType = $this->_getIdGeneratorTypeString($metadata->generatorType)) {
            $ids[$metadata->getSingleIdentifierFieldName()]['generator']['strategy'] = $idGeneratorType;
        }

        $array['id'] = $ids;

        if ($fieldMappings) {
            if ( ! isset($array['fields'])) {
                $array['fields'] = array();
            }
            $array['fields'] = array_merge($array['fields'], $fieldMappings);
        }

        foreach ($metadata->associationMappings as $name => $associationMapping) {
            $cascade = array();

            if ($associationMapping['isCascadeRemove']) {
                $cascade[] = 'remove';
            }

            if ($associationMapping['isCascadePersist']) {
                $cascade[] = 'persist';
            }

            if ($associationMapping['isCascadeRefresh']) {
                $cascade[] = 'refresh';
            }

            if ($associationMapping['isCascadeMerge']) {
                $cascade[] = 'merge';
            }

            if ($associationMapping['isCascadeDetach']) {
                $cascade[] = 'detach';
            }
            if (count($cascade) === 5) {
                $cascade = array('all');
            }

            $associationMappingArray = array(
                'targetEntity' => $associationMapping['targetEntity'],
                'cascade'     => $cascade,
            );

            if (isset($mapping['id']) && $mapping['id'] === true) {
                $array['id'][$name]['associationKey'] = true;
            }

            if ($associationMapping['type'] & ClassMetadataInfo::TO_ONE) {
                $joinColumns = $associationMapping['joinColumns'];
                $newJoinColumns = array();

                foreach ($joinColumns as $joinColumn) {
                    $newJoinColumns[$joinColumn['name']]['referencedColumnName'] = $joinColumn['referencedColumnName'];

                    if (isset($joinColumn['onDelete'])) {
                        $newJoinColumns[$joinColumn['name']]['onDelete'] = $joinColumn['onDelete'];
                    }
                }

                $oneToOneMappingArray = array(
                    'mappedBy'      => $associationMapping['mappedBy'],
                    'inversedBy'    => $associationMapping['inversedBy'],
                    'joinColumns'   => $newJoinColumns,
                    'orphanRemoval' => $associationMapping['orphanRemoval'],
                );

                $associationMappingArray = array_merge($associationMappingArray, $oneToOneMappingArray);

                if ($associationMapping['type'] & ClassMetadataInfo::ONE_TO_ONE) {
                    $array['oneToOne'][$name] = $associationMappingArray;
                } else {
                    $array['manyToOne'][$name] = $associationMappingArray;
                }
            } elseif ($associationMapping['type'] == ClassMetadataInfo::ONE_TO_MANY) {
                $oneToManyMappingArray = array(
                    'mappedBy'      => $associationMapping['mappedBy'],
                    'inversedBy'    => $associationMapping['inversedBy'],
                    'orphanRemoval' => $associationMapping['orphanRemoval'],
                    'orderBy'       => isset($associationMapping['orderBy']) ? $associationMapping['orderBy'] : null
                );

                $associationMappingArray = array_merge($associationMappingArray, $oneToManyMappingArray);
                $array['oneToMany'][$name] = $associationMappingArray;
            } elseif ($associationMapping['type'] == ClassMetadataInfo::MANY_TO_MANY) {
                $manyToManyMappingArray = array(
                    'mappedBy'   => $associationMapping['mappedBy'],
                    'inversedBy' => $associationMapping['inversedBy'],
                    'joinTable'  => isset($associationMapping['joinTable']) ? $associationMapping['joinTable'] : null,
                    'orderBy'    => isset($associationMapping['orderBy']) ? $associationMapping['orderBy'] : null
                );

                $associationMappingArray = array_merge($associationMappingArray, $manyToManyMappingArray);
                $array['manyToMany'][$name] = $associationMappingArray;
            }
        }

        return json_encode(array($metadata->name => $array));
    }
}
