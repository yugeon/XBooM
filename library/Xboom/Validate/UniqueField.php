<?php
/**
 *  CMF for web applications based on Zend Framework 1 and Doctrine 2
 *  Copyright (C) 2010  Eugene Gruzdev aka yugeon
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright  Copyright (c) 2010 yugeon <yugeon.ru@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html  GNU GPLv3
 */

/**
 * Unique field validator.
 *
 * @author yugeon
 */
namespace Xboom\Validate;

class UniqueField extends \Zend_Validate_Abstract
{
    /**
     * Error constants
     */
    const ERROR_NOT_UNIQUE = 'notUniqueField';

    /**
     * @var array Message templates
     */
    protected $_messageTemplates = array(
        self::ERROR_NOT_UNIQUE => "Not unique value '%value%'",
    );


    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected  $_em = null;

    /**
     *
     * @var string
     */
    protected $_entity;

    /**
     *
     * @var string
     */
    protected $_field;

    /**
     *
     * @param EntityManager $em
     * @return UniqueField
     * @throws Zend_Validate_Exception
     */
    public function setEntityManager($em)
    {
//        if (!($em instanceof \Doctrine\ORM\EntityManager))
//        {
//            throw new \Zend_Validate_Exception('Option em must be a Entity Manager instance');
//        }

        $this->_em = $em;
        return $this;
    }

    public function setEntityName($entityName)
    {
        $this->_entity = $entityName;
        return $this;
    }

    public function setFieldName($fieldName)
    {
        $this->_field = $fieldName;
    }

    /**
     * The following option keys are supported:
     * 'em'      => EntityManager
     * 'entity'  => The name of entity
     * 'field'   => The unique field
     * 
     * @param array|Zend_Config $options Options to use for this validator.
     */
    public function  __construct($options)
    {
        if ($options instanceof \Zend_Config)
        {
            $options = $options->toArray();
        }

        if (!\array_key_exists('em', $options))
        {
            throw new \Zend_Validate_Exception('Entity Manager option missing!');
        }
        elseif (!\array_key_exists('entity', $options))
        {
            throw new \Zend_Validate_Exception('Entity option missing!');
        }
        elseif (!\array_key_exists('field', $options))
        {
            throw new \Zend_Validate_Exception('Field option missing!');
        }

        $this->setEntityManager($options['em']);
        $this->setEntityName($options['entity']);
        $this->setFieldName($options['field']);
    }

    protected function _query($value)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('entity')
           ->from($this->_entity, 'entity')
           ->where($qb->expr()->eq('entity.' . $this->_field, '?1'))
           ->setParameter('1', $value);
        $query = $qb->getQuery();

        $result = $query->execute();
        return $result;
    }

    /**
     * Confirms a entity field value is unique.
     *
     * @param <type> $value
     * @return boolean
     */
    public function  isValid($value)
    {
        $valid = true;
        $this->_setValue($value);

        $result = $this->_query($value);

        if ($result)
        {
            $valid = false;
            $this->_error(self::ERROR_NOT_UNIQUE);
        }
        
        return $valid;
    }
    
}
