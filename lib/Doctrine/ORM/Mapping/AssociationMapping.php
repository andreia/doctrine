<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */

namespace Doctrine\ORM\Mapping;

/**
 * Base class for association mappings.
 *
 * @author Roman Borschel <roman@code-factory.org>
 * @since 2.0
 */
abstract class AssociationMapping
{
    const FETCH_MANUAL = 1;
    const FETCH_LAZY = 2;
    const FETCH_EAGER = 3;
    
    /**
     * Cascade types enumeration.
     *
     * @var array
     */
    protected static $_cascadeTypes = array(
        'all',
        'none',
        'save',
        'delete',
        'refresh'
    );
    
    protected $_cascades = array();
    protected $_isCascadeDelete;
    protected $_isCascadeSave;
    protected $_isCascadeRefresh;
    
    /**
     * The fetch mode used for the association.
     *
     * @var integer
     */
    protected $_fetchMode = self::FETCH_MANUAL;
    
    /**
     * Flag that indicates whether the class that defines this mapping is
     * the owning side of the association.
     *
     * @var boolean
     */
    protected $_isOwningSide = true;
    
    /**
     * Whether the association is optional (0..X) or not (1..X).
     * By default all associations are optional.
     *
     * @var boolean
     */
    protected $_isOptional = true;
    
    /**
     * The name of the source Entity (the Entity that defines this mapping).
     *
     * @var string
     */
    protected $_sourceEntityName;
    
    /**
     * The name of the target Entity (the Enitity that is the target of the
     * association).
     *
     * @var string
     */
    protected $_targetEntityName;
    
    /**
     * Identifies the field on the source class (the class this AssociationMapping
     * belongs to) that represents the association and stores the reference to the
     * other entity/entities.
     *
     * @var string
     */
    protected $_sourceFieldName;
    
    /**
     * Identifies the field on the owning side that controls the mapping for the
     * association. This is only set on the inverse side of an association.
     *
     * @var string
     */
    protected $_mappedByFieldName;
    
    /**
     * The join table definition, if any.
     *
     * @var array
     */
    protected $_joinTable = array();
    
    /**
     * Initializes a new instance of a class derived from AssociationMapping.
     *
     * @param array $mapping  The mapping definition.
     */
    public function __construct(array $mapping)
    {
        $this->_validateAndCompleteMapping($mapping);
    }
    
    /**
     * Validates & completes the mapping. Mapping defaults are applied here.
     *
     * @param array $mapping
     */
    protected function _validateAndCompleteMapping(array $mapping)
    {        
        // Mandatory attributes for both sides
        if ( ! isset($mapping['fieldName'])) {
            throw MappingException::missingFieldName();
        }
        $this->_sourceFieldName = $mapping['fieldName'];
        
        if ( ! isset($mapping['sourceEntity'])) {
            throw MappingException::missingSourceEntity($mapping['fieldName']);
        }
        $this->_sourceEntityName = $mapping['sourceEntity'];
        
        if ( ! isset($mapping['targetEntity'])) {
            throw MappingException::missingTargetEntity($mapping['fieldName']);
        }
        $this->_targetEntityName = $mapping['targetEntity'];
        
        // Mandatory and optional attributes for either side
        if ( ! isset($mapping['mappedBy'])) {            
            // Optional
            if (isset($mapping['joinTable'])) {
                $this->_joinTable = $mapping['joinTable'];   
            }
        } else {
            $this->_isOwningSide = false;
            $this->_mappedByFieldName = $mapping['mappedBy'];
        }
        
        // Optional attributes for both sides
        $this->_isOptional = isset($mapping['optional']) ?
                (bool)$mapping['optional'] : true;
        $this->_cascades = isset($mapping['cascade']) ?
                (array)$mapping['cascade'] : array();
    }
    
    /**
     * Whether the association cascades delete() operations from the source entity
     * to the target entity/entities.
     *
     * @return boolean
     */
    public function isCascadeDelete()
    {
        if (is_null($this->_isCascadeDelete)) {
            $this->_isCascadeDelete = in_array('delete', $this->_cascades);
        }
        return $this->_isCascadeDelete;
    }
    
    /**
     * Whether the association cascades save() operations from the source entity
     * to the target entity/entities.
     *
     * @return boolean
     */
    public function isCascadeSave()
    {
        if (is_null($this->_isCascadeSave)) {
            $this->_isCascadeSave = in_array('save', $this->_cascades);
        }
        return $this->_isCascadeSave;
    }
    
    /**
     * Whether the association cascades refresh() operations from the source entity
     * to the target entity/entities.
     *
     * @return boolean
     */
    public function isCascadeRefresh()
    {
        if (is_null($this->_isCascadeRefresh)) {
            $this->_isCascadeRefresh = in_array('refresh', $this->_cascades);
        }
        return $this->_isCascadeRefresh;
    }
    
    /**
     * Whether the target entity/entities of the association are eagerly fetched.
     *
     * @return boolean
     */
    public function isEagerlyFetched()
    {
        return $this->_fetchMode == self::FETCH_EAGER;
    }
    
    /**
     * Whether the target entity/entities of the association are lazily fetched.
     *
     * @return boolean
     */
    public function isLazilyFetched()
    {
        return $this->_fetchMode == self::FETCH_LAZY;
    }
    
    /**
     * Whether the target entity/entities of the association are manually fetched.
     *
     * @return boolean
     */
    public function isManuallyFetched()
    {
        return $this->_fetchMode == self::FETCH_MANUAL;
    }
    
    /**
     * Whether the source entity of this association represents the owning side.
     *
     * @return boolean
     */
    public function isOwningSide()
    {
        return $this->_isOwningSide;
    }
    
    /**
     * Whether the source entity of this association represents the inverse side.
     *
     * @return boolean
     */
    public function isInverseSide()
    {
        return ! $this->_isOwningSide;
    }
    
    /**
     * Whether the association is optional (0..X), or not (1..X).
     *
     * @return boolean TRUE if the association is optional, FALSE otherwise.
     */
    public function isOptional()
    {
        return $this->_isOptional;
    }
    
    /**
     * Gets the name of the source entity class.
     *
     * @return string
     */
    public function getSourceEntityName()
    {
        return $this->_sourceEntityName;
    }
    
    /**
     * Gets the name of the target entity class.
     *
     * @return string
     */
    public function getTargetEntityName()
    {
        return $this->_targetEntityName;
    }
    
    /**
     * Gets the join table definition, if any.
     *
     * @return array
     */
    public function getJoinTable()
    {
        return $this->_joinTable;
    }
    
    /**
     * Get the name of the field the association is mapped into.
     *
     * @return string
     */
    public function getSourceFieldName()
    {
        return $this->_sourceFieldName;
    }
    
    /**
     * Gets the field name of the owning side in a bi-directional association.
     *
     * @return string
     */
    public function getMappedByFieldName()
    {
        return $this->_mappedByFieldName;
    }
    
    public function isOneToOne()
    {
        return false;
    }
    
    public function isOneToMany()
    {
        return false;
    }
    
    public function isManyToMany()
    {
        return false;
    }

    public function usesJoinTable()
    {
        return (bool)$this->_joinTable;
    }

    abstract public function lazyLoadFor($entity, $entityManager);
}

