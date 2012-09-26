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

    public static function isValid($value) {
        return $value == LocationType::Location ||
               $value == LocationType::Building ||
               $value == LocationType::PointOfInterest;
    }
}

/**
 * @Entity @Table(name="locations")
 */
class Location extends Base {
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


    public static function create($em, $data) {
        // For now we just assume perfect data
        // TODO: Add validation
        $location = new Location();
        $location->setDistrict($em->find("Entities\District", (int) $data["districtId"]));
        //$location->
    }
}