<?php
namespace Entities;

use Doctrine\Common\Collections\ArrayCollection;
/**
 * @Entity @Table(name="districts")
 */
class District extends Base implements \JsonSerializable {

    /**
     * @ManyToOne(targetEntity="Entities\Region", inversedBy="districts")
     * @JoinColumn(name="region_id", referencedColumnName="id", onDelete="CASCADE")
     * @var Entities\Region
     */
    protected $region;

    /**
     * @Column(type="text")
     * @var string
     */
    protected $polygon;

    /**
     *
     * @OneToMany(targetEntity="Entities\Location", mappedBy="locations")
     * @var Entities\Location[]
     */
    protected $locations = null;

    /**
     *
     * @OneToMany(targetEntity="Entities\PointOfInterest", mappedBy="pointOfInterests")
     * @var Entities\PointOfInterest[]
     */
    protected $pointOfInterests = null;

    /**
     *
     * @OneToMany(targetEntity="Entities\Building", mappedBy="buildings")
     * @var Entities\buildings[]
     */
    protected $buildings = null;

    public function __construct() {
        $this->locations         = new ArrayCollection();
        $this->pointsOfInterests = new ArrayCollection();
        $this->buildings         = new ArrayCollection();
    }

    public static function create($em, $data) {
        // For now we assume just perfect data
        // This must of course be filered later on
        $region   = $em->find('Entities\Region', (int) $data['region_id']);
        $district = new District();
        $district->setRegion($region);
        $district->setName($data['name']);
        $district->setDescription($data['description']);
        $district->setPolygon($data['polygon']);

        return $district;
    }

    public function getRegion() {
        return $this->region;
    }
    public function setRegion($region) {
        $this->region = $region;
    }

    public function getPolygon() {
        return $this->polygon;
    }
    public function setPolygon($polygon) {
        $this->polygon = $polygon;
    }

    public function getLocations() {
        return $this->locations;
    }
    public function addLocation($location) {
        $this->locations[] = $location;
    }

    public function getPointOfInterests() {
        return $this->pointOfInterests;
    }
    public function addPointOfInterest($poi) {
        $this->pointOfInterest[] = $poi;
    }

    public function getBuildings() {
        return $this->buildings;
    }
    public function addBuilding($building) {
        $this->buildings[] = $building;
    }

    public function jsonSerialize() {
        return array(
            "id"      => $this->getId(),
            "name"    => $this->getName(),
            "polygon" => $this->getPolygon()
        );
    }
}