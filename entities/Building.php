<?php
namespace Entities;

/**
 * @Entity @Table(name="buildings")
 */
class Building extends Point {

    /**
     * @ManyToOne(targetEntity="Entities\Region", inversedBy="buildings")
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