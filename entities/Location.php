<?php
namespace Entities;

/**
 * @Entity @Table(name="locations")
 */
class Location extends Point {

    /**
     * @ManyToOne(targetEntity="Entities\Region", inversedBy="locations")
     * @var Entities\District
     */
    protected $district;

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