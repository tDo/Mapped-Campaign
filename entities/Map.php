<?php
namespace Entities;

use Doctrine\Common\Collections\ArrayCollection;
/**
 * @Entity @Table(name="maps")
 */
class Map {

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
     * @Column(type="string")
     * @var string
     **/
    protected $path;

    /**
     *
     * @OneToMany(targetEntity="Entities\Region", mappedBy="map")
     * @var Entities\Region[]
     */
    protected $regions = null;

    public function __construct() {
        $this->regions = new ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;
    }

    public function getRegions() {
        return $this->regions;
    }

    public function addRegion($region) {
        $this->regions[] = $region;
    }
}