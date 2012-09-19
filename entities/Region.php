<?php
namespace Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="regions")
 */
class Region extends Base implements \JsonSerializable {
    /**
     * @ManyToOne(targetEntity="Entities\Map", inversedBy="regions")
     * @JoinColumn(name="map_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Entities\Map
     */
    protected $map;

    /**
     *
     * @OneToMany(targetEntity="Entities\District", mappedBy="region")
     * @var Entities\District[]
     */
    protected $districts = null;

    public function __construct() {
        $this->districts = new ArrayCollection();
    }

    public function getMap() {
        return $this->map;
    }

    public function setMap($map) {
        $this->map = $map;
    }

    public function getDistricts() {
        return $this->districts;
    }

    public function addDistrict($district) {
        $this->districts[] = $district;
    }

    public function jsonSerialize() {
        $result = array(
            "id"        => $this->getId(),
            "name"      => $this->getName(),
            "districts" => $this->getDistricts()->toArray()
        );

        if ($this->getFullySerialize())
            $result['description'] = $this->getDescription();

        return $result;
    }
}