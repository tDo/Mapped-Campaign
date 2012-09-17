<?php
namespace Entities;

/**
 * @Entity @Table(name="regions")
 */
class Region {
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
     * @ManyToOne(targetEntity="Entities\Map", inversedBy="regions")
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

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
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
}