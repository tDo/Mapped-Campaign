<?php
namespace Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entity-Class represents a map which holds a name, a path where the
 * map tiles are stored as well as subregions, districts and submarkers (Like locations, Points of interest and buildings).
 * 
 * @Entity @Table(name="maps")
 */
class Map extends Base implements \JsonSerializable {

    /** 
     * Database-Id of the map
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     **/
    protected $id;

    /** 
     * Name of the map
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
     * Bound regions, mapped against database as a one to many association.
     * Will load the bound regions as required
     * 
     * @OneToMany(targetEntity="Entities\Region", mappedBy="map")
     * @var Entities\Region[]
     */
    protected $regions = null;

    /**
     * Creates a new instance of the map and presets the regions as empty
     */
    public function __construct() {
        $this->regions = new ArrayCollection();
    }

    /**
     * Retrieve the database-id for this map
     * @return int Id of the map
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Retrieve the associated map name
     * @return string Name of the map
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set the associated map name
     * @param string $name Name of the map
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Retrieve the images sub path where the map tiles are stored.
     * The frontend will then automatically retrieve the tiles from
     * that provided folder.
     * @return string Subpath where the map-tiles are stored
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Set the images sub path where the map-tiles are stored
     * @param string $path Subpath where the map-tiles are stored
     */
    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * Retrieve the regions on this map from the database association
     * @return Entities\Region[] List of all regions associated with this map
     */
    public function getRegions() {
        return $this->regions;
    }

    /**
     * Add a new region to this map
     * @param Entities\Region $region The region to add to this map
     */
    public function addRegion($region) {
        $this->regions[] = $region;
    }

    /**
     * Serialize the map and all regions, districts, etc. on it as a single
     * json string. Based on the FullySerialize flag the description of the
     * map itsself will be added or not (Note that the descriptions of the
     * subelements will NOT be added and must be handled per entry)
     * @return string JSON-representation of the map
     */
    public function jsonSerialize() {
        $result = array(
            "id"      => $this->getId(),
            "name"    => $this->getName(),
            "path"    => $this->getPath(),
            "regions" => $this->getRegions()->toArray()
        );

        if ($this->getFullySerialize())
            $result['description'] = $this->getDescription();

        return $result;  
    }

}