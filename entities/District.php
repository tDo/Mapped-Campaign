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

    private static function isValidPolygon($json) {
        try {
            $polygon = json_decode($json);
            // Must be an array
            if (!is_array($polygon)) return false;
            // Hold at least 3 entries (3 points are the minimum for a polygon)
            if (count($polygon) < 3) return false;

            // And all fields must hold positions
            $filter = require __DIR__ .'/../vendor/aura/filter/scripts/instance.php';
            $filter->addSoftRule('Xa', $filter::IS, 'float');
            $filter->addSoftRule('Ya', $filter::IS, 'float');

            foreach ($polygon as $point) {
                // if the point filter fails, we stop (Not a valid point)
                if (!$filter->values($point)) return false;
            }

            // If we got here, the value seems fine enough
            return true;
        } catch (\Exception $ex) {
            // Something failed => invalid
            return false;
        }
    }

    /**
     *
     * @OneToMany(targetEntity="Entities\Location", mappedBy="locations")
     * @var Entities\Location[]
     */
    protected $locations = null;


    public function __construct() {
        $this->locations         = new ArrayCollection();
    }

    private static function assign($em, $data, $requiresDistrictId = false) {
        // Retrieve the filters (Pre initialized)
        $filter = require __DIR__ .'/../vendor/aura/filter/scripts/instance.php';

        // And add rules to the filter
        // The region id must be an integer, > 0 and present
        $filter->addSoftRule('region_id', $filter::IS, 'int');
        $filter->addSoftRule('region_id', $filter::IS, 'min', 1);

        // A name must not be blank and have at least some characters in it (But may not exceed the allowed maximum)
        $filter->addSoftRule('name', $filter::IS_NOT, 'blank');
        $filter->addSoftRule('name', $filter::IS, 'strlenBetween', 1, 255);

        // The description must not be empty and hold at least a single character
        $filter->addSoftRule('description', $filter::IS_NOT, 'blank');
        $filter->addSoftRule('description', $filter::IS, 'strlenMin', 1);

        // For the polygon we at least require a value
        // Further validation is done later on
        $filter->addSoftRule('polygon', $filter::IS_NOT, 'blank'); 
        
        // Is a district-id required? (For editing)
        if ($requiresDistrictId) {
            // Must be an integer and > 0
            $filter->addSoftRule('district_id', $filter::IS, 'int');
            $filter->addSoftRule('district_id', $filter::IS, 'min', 1);
        }

        if (!$filter->values($data))
            // Even basic filtering failed...
            throw new EntityException("Invalid data supplied", array_keys($filter->getMessages()));

        // The basic filters matched
        // Is the polygon valid?
        if (!District::isValidPolygon($data['polygon']))
            throw new  EntityException("Invalid district polygon passed", "");
        
        // Is the region known?
        $region = $em->find('Entities\Region', (int) $data['region_id']);
        if (!$region)
            // Could not find the region supplied
            throw new EntityException("Region supplied is unknown", "region_id", 404);

        // The region seems fine as well
        // We preset the district with a new one for now
        $district = new District(); 
        
        // Is a known district required?
        if ($requiresDistrictId) {
            $district = $em->find('Entities\District', (int) $data['district_id']);
            if (!$district) {
                // District seems invalid...
                throw new EntityException("District supplied is unknown", "district_id", 404);
            }
        }

        // If we reached here, that was just fine, assign the data
        $district->setRegion($region);
        $district->setName($data['name']);
        $district->setDescription($data['description']);
        $district->setPolygon($data['polygon']);

        // Create transaction and try to save the value
        $em->getConnection()->beginTransaction();
        try {
            // Try to persist the value
            $em->persist($district);
            $em->flush();
            $em->getConnection()->commit();

            // Was saved and we are done
            return $district;

        } catch (Exception $ex) {
            // Something failed, rollback transaction and close the connection
            $em->getConnection()->rollback();
            $em->close();

            // We just failed saving the data (But rolled back)
            throw new EntityException("Failed to save district", "", 500);
        }
    }

    public static function create($em, $data) {
        return District::assign($em, $data, false);
    }

    public static function edit($em, $data) {
        return District::assign($em, $data, true);
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

    public function jsonSerialize() {
        $result = array(
            "id"        => $this->getId(),
            "name"      => $this->getName(),
            "polygon" => $this->getPolygon()        
        );

        if ($this->getFullySerialize())
            $result['description'] = $this->getDescription();

        return $result;
    }
}