<?php
namespace Entities;

use Doctrine\Common\Collections\ArrayCollection;
/**
 * @Entity @Table(name="districts")
 */
class District {

    /** 
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     **/

    protected $id;

    /** 
     * @Column(type="string") 
     * @var string
     **/
    protected $name;

    /**
     * @ManyToOne(targetEntity="Entities\Region", inversedBy="districts")
     * @var Entities\Region
     */
    protected $region;

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

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getRegion() {
        return $this->region;
    }

    public function setRegion($region) {
        $this->region = $region;
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
}