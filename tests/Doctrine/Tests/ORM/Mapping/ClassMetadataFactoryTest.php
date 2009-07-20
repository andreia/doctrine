<?php

namespace Doctrine\Tests\ORM\Mapping;

use Doctrine\Tests\Mocks\MetadataDriverMock;
use Doctrine\Tests\Mocks\DatabasePlatformMock;
use Doctrine\Tests\Mocks\EntityManagerMock;
use Doctrine\Tests\Mocks\ConnectionMock;
use Doctrine\Tests\Mocks\DriverMock;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Common\EventManager;

require_once __DIR__ . '/../../TestInit.php';

class ClassMetadataFactoryTest extends \Doctrine\Tests\OrmTestCase
{
    public function testGetMetadataForSingleClass()
    {
        $driverMock = new DriverMock();
        $config = new \Doctrine\ORM\Configuration();
        $eventManager = new EventManager();
        $conn = new ConnectionMock(array(), $driverMock, $config, $eventManager);
        $mockDriver = new MetadataDriverMock();
        $config->setMetadataDriverImpl($mockDriver);

        $entityManager = EntityManagerMock::create($conn, $config, $eventManager);

        $mockPlatform = $conn->getDatabasePlatform();
        $mockPlatform->setPrefersSequences(true);
        $mockPlatform->setPrefersIdentityColumns(false);

        // Self-made metadata
        $cm1 = new ClassMetadata('Doctrine\Tests\ORM\Mapping\TestEntity1');
        // Add a mapped field
        $cm1->mapField(array('fieldName' => 'name', 'type' => 'varchar'));
        // Add a mapped field
        $cm1->mapField(array('fieldName' => 'id', 'type' => 'integer', 'id' => true));
        // and a mapped association
        $cm1->mapOneToOne(array('fieldName' => 'other', 'targetEntity' => 'Other', 'mappedBy' => 'this'));
        // and an association on the owning side
        $joinColumns = array(
            array('name' => 'other_id', 'referencedColumnName' => 'id')
        );
        $cm1->mapOneToOne(array('fieldName' => 'association', 'targetEntity' => 'Other', 'joinColumns' => $joinColumns));
        // and an id generator type
        $cm1->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_AUTO);

        // SUT
        $cmf = new ClassMetadataFactoryTestSubject($entityManager);
        $cmf->setMetadataForClass('Doctrine\Tests\ORM\Mapping\TestEntity1', $cm1);

        // Prechecks
        $this->assertEquals(array(), $cm1->parentClasses);
        $this->assertEquals(ClassMetadata::INHERITANCE_TYPE_NONE, $cm1->inheritanceType);
        $this->assertTrue($cm1->hasField('name'));
        $this->assertEquals(2, count($cm1->associationMappings));
        $this->assertEquals(ClassMetadata::GENERATOR_TYPE_AUTO, $cm1->generatorType);

        // Go
        $cm1 = $cmf->getMetadataFor('Doctrine\Tests\ORM\Mapping\TestEntity1');

        $this->assertEquals(array(), $cm1->parentClasses);
        $this->assertEquals(array('other_id'), $cm1->joinColumnNames);
        $this->assertTrue($cm1->hasField('name'));
        $this->assertEquals(ClassMetadata::GENERATOR_TYPE_SEQUENCE, $cm1->generatorType);
    }
}

/* Test subject class with overriden factory method for mocking purposes */
class ClassMetadataFactoryTestSubject extends \Doctrine\ORM\Mapping\ClassMetadataFactory
{
    private $_mockMetadata = array();
    private $_requestedClasses = array();

    /** @override */
    protected function _newClassMetadataInstance($className)
    {
        $this->_requestedClasses[] = $className;
        if ( ! isset($this->_mockMetadata[$className])) {
            throw new InvalidArgumentException("No mock metadata found for class $className.");
        }
        return $this->_mockMetadata[$className];
    }

    public function setMetadataForClass($className, $metadata)
    {
        $this->_mockMetadata[$className] = $metadata;
    }

    public function getRequestedClasses()
    {
        return $this->_requestedClasses;
    }
}

class TestEntity1
{
    private $id;
    private $name;
    private $other;
    private $association;
}
