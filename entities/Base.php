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

    /**
     * Flag deciding wether a json serialize shall serialize all
     * values or just a partial of the whole instance. This is used
     * to reduce costs when not all data is required (Like long descriptions
     * when we only wish to retrieve a basic overview of the map instances)
     * @var boolean
     */
    private $fullySerialize = false;

    public function setFullySerialize($val) {
        if (!is_bool($val))
            throw new InvalidArgumentException('Fully Serialize only accepts boolean arguments');

        $this->fullySerialize = $val;
    }
    public function getFullySerialize() {
        return $this->fullySerialize;
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

    public function getDescription() {
        return $this->description;
    }
    public function setDescription($description) {
        $this->description = $description;
    }
}