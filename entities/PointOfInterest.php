<?php
namespace Entities;

/**
 * @Entity @Table(name="pointsofinterest")
 */
class PointOfInterest extends Point {
    /**
     * @ManyToOne(targetEntity="Entities\Region", inversedBy="pointOfInterests")
     * @var Entities\District
     */
    protected $district;

    public function getDistrict() {
        return $this->districts;
    }

    public function setDistrict($district) {
        $this->district = $district;
    }
}