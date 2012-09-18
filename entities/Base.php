<?php
namespace Entities;

/**
 * Overall base class which implements the definitions for name and
 * id fields
 */
abstract class Base {
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
     * @Column(type="text")
     * @var string
     */
    protected $description;

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getDescription() {
        return $this->description;
    }
    public function setDescription($description) {
        $this->description = $description;
    }
}