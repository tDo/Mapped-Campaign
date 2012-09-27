<?php
namespace Entities;

/**
 * Enumeration class which holds the possible location
 * types a location might have
 */
class LocationType {
    /**
     * Default location (Like a city)
     */
    const Location        = 0;
    /**
     * Represents a building
     */
    const Building        = 1;
    /**
     * Represents a point of interest
     */
    const PointOfInterest = 2;

    /**
     * Retrieve an array holding all possible types
     * @return int[] Array of all location types
     */
    public static function toArray() {
        return array(LocationType::Location,
                     LocationType::Building,
                     LocationType::PointOfInterest);
    }

    /**
     * Function checks if the provided type is valid
     * @param  int      $type Value to verify
     * @return boolean        Wether it is a known location type or not
     */
    public static function isValid($type) {
        return in_array($type, LocationType::toArray());
    }
}

/**
 * @Entity @Table(name="locations")
 */
class Location extends Base implements \JsonSerializable {
    /** 
     * X-Position on the map
     * @Column(type="integer")
     * @var int
     **/
    protected $x;

    /** 
     * Y-Position on the map
     * @Column(type="integer")
     * @var int
     **/
    protected $y;

    /**
     * Type of the location
     * @Column(type="integer")
     * @var integer
     */
    protected $type;

    /**
     * @ManyToOne(targetEntity="Entities\Region", inversedBy="locations")
     * @JoinColumn(name="district_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Entities\District
     */
    protected $district;

    public function __construct() {
        $this->type = LocationType::Location;
    }

    private static function assign($em, $data, $requiresLocationId = false) {
        // Retrieve the filters (Pre initialized)
        $filter = require __DIR__ .'/../vendor/aura/filter/scripts/instance.php';

        // Add rules to the filter
        // District id must be given and > 0
        $filter->addSoftRule('district_id', $filter::IS, 'int');
        $filter->addSoftRule('district_id', $filter::IS, 'min', 1);

        if ($requiresLocationId) {
            $filter->addSoftRule('location_id', $filter::IS, 'int');
            $filter->addSoftRule('location_id', $filter::IS, 'min', 1);
        }

        // Name must not be blank or too long
        $filter->addSoftRule('name', $filter::IS_NOT, 'blank');
        $filter->addSoftRule('name', $filter::IS, 'strlenBetween', 1, 255);

        // The description must not be empty and hold at least a single character
        $filter->addSoftRule('description', $filter::IS_NOT, 'blank');
        $filter->addSoftRule('description', $filter::IS, 'strlenMin', 1);

        // A type must be present and in the allowed location type values
        $filter->addSoftRule('type', $filter::IS, 'inValues', LocationType::toArray());

        // A location as x/y coordinates must be given
        $filter->addSoftRule('x', $filter::IS, 'float');
        $filter->addSoftRule('y', $filter::IS, 'float');

        if (!$filter->values($data))
            // The basic filtering was invalid
            throw new EntityException("Invalid data supplied", array_keys($filter->getMessages()));

        // Basic filters seemed fine, is the district known?
        $district = $em->find('Entities\District', (int) $data['district_id']);
        if (!$district)
            // Could not find the district supplied
            throw new EntityException('District supplied is unknown', 'district_id', 404);

        // The district was found
        // TODO: Add check to find out wether the location is in the district's polygon or not
        
        // preset the location with a new one for now
        $location = new Location();

        // Is a known location required?
        if ($requiresLocationId) {
            $location = $em->find('Entities\Location', (int) $data['location_id']);
            if (!$location)
                // Location seems invalid
                throw new EntityException('Location supplied is unknown', 'location_id', 404);
        }

        // If we got here, everything seems fine: assign the data
        $location->setDistrict($district);
        $location->setName($data['name']);
        $location->setDescription($data['description']);
        $location->setType($data['type']);
        $location->setX($data['x']);
        $location->setY($data['y']);

        // Create transaction and try to save the value
        $em->getConnection()->beginTransaction();
        try {
            // Try to persist the value
            $em->persist($location);
            $em->flush();
            $em->getConnection()->commit();

            // Was saved and we are done
            return $location;

        } catch (Exception $ex) {
            // Something failed, rollback transaction and close connection
            $em->getConnection()->rollback();
            $em->close();

            // Failed saving the data, but rolled back
            throw new EntityException('Failed to save location', '', 500);
        }
    }

    public static function create($em, $data) {
        return Location::assign($em, $data, false);
    }

    public static function edit($em, $data) {
        return Location::assign($em, $data, true);
    }

    /**
     * Retrieve the X-Position of this entity on the map
     * @return float X-Position on the map
     */
    public function getX() {
        return $this->x;
    }
    /**
     * Set the X-Position of this entity on the map
     * @param float $x X-Position on the map
     */
    public function setX($x) {
        $this->x = $x;
    }

    /**
     * Retrieve the Y-Position of this entity on the map
     * @return float Y-Position on the map
     */
    public function getY() {
        return $this->y;
    }
    /**
     * Set the Y-Position of this entity on the map
     * @param float $y Y-Position on the map
     */
    public function setY($y) {
        $this->y = $y;
    }

    public function setType($type) {
        if (LocationType::isValid($type))
            $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

    public function getDistrict() {
        return $this->districts;
    }
    public function setDistrict($district) {
        $this->district = $district;
    }

    public function jsonSerialize() {
        $result = array(
            "id"    => $this->getId(),
            "name"  => $this->getName(),
            "type"  => $this->getType(),
            "x"     => $this->getX(),
            "y"     => $this->getY()     
        );

        if ($this->getFullySerialize())
            $result['description'] = $this->getDescription();

        return $result;
    }
}
