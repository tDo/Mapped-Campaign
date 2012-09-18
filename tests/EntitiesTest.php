<?php
require __DIR__ .'/../vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * This test fixture tests the entities models for their correct behavior.
 * Like creating entities, linking them together and removing them again.
 * It also verifies that the helper methods applied to the entities work as
 * expected.
 */
class EntitiesTest extends PHPUnit_Framework_TestCase {
    
    private static $em;
    private static $classes;

    /**
     * Static function will set up the database structure before actually
     * running any of the tests. That way a configure database connection exists
     * which can be used in each of the test cases
     */
    public static function setUpBeforeClass() {
        // TODO: Get the params from a config file
        $dbParams = array(
            'driver'   => 'pdo_mysql',
            'user'     => 'mytho',
            'password' => 'wiki',
            'dbname'   => 'campaignTest',
        );
        $devMode = true;

        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ .'/../entities'), $devMode);
        self::$em     = EntityManager::create($dbParams, $config);

        $classLoader = new \Doctrine\Common\ClassLoader('Entities', __DIR__ . '/..', 'loadClass');
        $classLoader->register();

        // Retrieve classes schema once
        self::$classes = array(
            self::$em->getClassMetadata('Entities\Map'),
            self::$em->getClassMetadata('Entities\Region'),
            self::$em->getClassMetadata('Entities\District'),
            self::$em->getClassMetadata('Entities\Location'),
            self::$em->getClassMetadata('Entities\PointOfInterest'),
            self::$em->getClassMetadata('Entities\Building')
        );

        self::refreshSchema();
    }

    public static function refreshSchema() {
        $tool = new \Doctrine\ORM\Tools\SchemaTool(self::$em);
        $tool->dropSchema(self::$classes);
        $tool->createSchema(self::$classes);
    }

    /**
     * Function called after each test
     */
    public function tearDown() {
        // Refresh the schema
        self::refreshSchema();
    }

    /**
     * Tear down method after each test in the class has finished
     * executing. WIll just close the db connection
     */
    public static function tearDownAfterClass() {
        self::$em = NULL;
    }

    /**
     * Function will create and persits a map instance which
     * can be used for further testing
     * @return Entities\Map Created and persited map instance
     */
    protected function createMap($name, $path) {
        $map = new Entities\Map();
        $map->setName($name);
        $map->setPath($path);

        self::$em->persist($map);
        self::$em->flush();

        return $map;
    }

    /**
     * Method will generate a region assigned to a specific map
     * @param  Entities\Map $map  The map the region shall be assigned to
     * @param  String $name Name of the region
     * @return Entities\Region       Presisted region instance
     */
    protected function createRegion($map, $name) {
        $region = new Entities\Region();
        $region->setMap($map);
        $region->setName($name);

        self::$em->persist($region);
        self::$em->flush();

        return $region;
    }

    protected function createDistrict($region, $name) {
        $district = new Entities\District();
        $district->setRegion($region);
        $district->setName($name);

        self::$em->persist($district);
        self::$em->flush();

        return $district;
    }

    public function testCanCreateMap() {
        $map = $this->createMap("Test Map", "TestMap");
        $this->assertGreaterThan(0, $map->getId(), "Expected a map id greater than 0 for the map");
    }

    
    public function testCanAddRegion() {
        // Check if a region can be added to a map
        $map = $this->createMap("Test Map", "TestMap");
        $region = $this->createRegion($map, "Test Region");

        $this->assertGreaterThan(0, $region->getId(), "Expected the region to have an id > 0");
        $this->assertEquals($map, $region->getMap());
        $this->assertEquals("Test Region", $region->getName());  
    }


    public function testCanRemoveRegion() {
        $map = $this->createMap("Test Map", "TestMap");
        $region = $this->createRegion($map, "Test Region");

        $id = $region->getId();

        self::$em->remove($region);
        self::$em->flush();

        $region = self::$em->find("Entities\Region", $id);

        $this->assertNull($region, "Expected region to be removed");
    }

 
    public function testRemovesRegionsWhenRemovingMap() {
        $map = $this->createMap("Test Map", "TestMap");
        $region = $this->createRegion($map, "Test Region");

        $id = $region->getId();

        self::$em->remove($map);
        self::$em->flush();
        self::$em->clear();

        $region = self::$em->find("Entities\Region", $id);

        $this->assertNull($region, "Expected region to be removed");
    }

    public function testCanAddDistrict() {
        $map = $this->createMap("Test Map", "TestMap");
        $region = $this->createRegion($map, "Test Region");
        $district = $this->createDistrict($region, "Test District");

        $this->assertGreaterThan(0, $district->getId(), "Expected the district to have an id > 0");
        $this->assertEquals($region, $district->getRegion());
        $this->assertEquals("Test District", $district->getName());  
    }

    public function testCanRemoveDistrict() {
        $map = $this->createMap("Test Map", "TestMap");
        $region = $this->createRegion($map, "Test Region");
        $district = $this->createDistrict($region, "Test District");

        $id = $district->getId();

        self::$em->remove($district);
        self::$em->flush();

        $district = self::$em->find("Entities\District", $id);

        $this->assertNull($district);
    }

    public function testRemovesDistrictsWhenRemovingRegion() {
        $map = $this->createMap("Test Map", "TestMap");
        $region = $this->createRegion($map, "Test Region");
        $district = $this->createDistrict($region, "Test District");

        $id = $district->getId();

        self::$em->remove($region);
        self::$em->flush();
        self::$em->clear();

        $district = self::$em->find("Entities\District", $id);

        $this->assertNull($district, "Expected district to be removed");
    }

}