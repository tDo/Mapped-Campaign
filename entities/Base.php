<?php
namespace Entities;

/**
 * Overall base class which implements the definitions for name and
 * id fields
 */
abstract class Base {
    /**
     * Id of the entity as stored in the database
     * 
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     **/
    protected $id;

    /** 
     * Name of the entity as stored in the database and displayed on the map
     * 
     * @Column(type="string") 
     * @var string
     **/
    protected $name;

    /**
     * Description text of the entity. Can be edited and will be displayed on the
     * information pages for this sepecific location.
     * 
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

    /**
     * Set wether json serialization shall include all information (e.g. descriptions) or just
     * the basic requirements. This is used to reduce serialization and transport costs by reducing
     * actual size.
     * @param bool $val Flag indicting if we require full serialization or only partial
     */
    public function setFullySerialize($val) {
        if (!is_bool($val))
            throw new InvalidArgumentException('Fully Serialize only accepts boolean arguments');

        $this->fullySerialize = $val;
    }
    /**
     * Get wether the instance shall be fully or partially serialized as json
     * @return bool Full serialization (true), partial (false)
     */
    public function getFullySerialize() {
        return $this->fullySerialize;
    }

    /**
     * Get the id of this entity as stored in the database
     * @return int Id of the entity
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get the name associated with this entity
     * @return string Name of the entity
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set the name of this entity
     * @param string $name Name to assign to this entity
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Retrieve the description text of this entity
     * @return string Description text of the entity
     */
    public function getDescription() {
        return $this->description;
    }
    /**
     * Set the entity description text
     * @param string $description Description text
     */
    public function setDescription($description) {
        $this->description = $description;
    }
}