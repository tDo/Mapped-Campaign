<?php
namespace Entities;

/**
 * @Entity @Table(name="buildings")
 */
class Building {
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
     * @ManyToOne(targetEntity="Entities\Region", inversedBy="buildings")
     * @var Entities\District
     */
    protected $district;

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getDistrict() {
        return $this->districts;
    }

    public function setDistrict($district) {
        $this->district = $district;
    }
}