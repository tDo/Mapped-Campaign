<?php
namespace Models;

class Map extends Base {

    /**
     * Defines the database table name maps are stored in
     * @var string
     */
    public static $_table = 'maps';

    /**
     * Retrieve a list of al regions on this map.
     * @return [type] [description]
     */
    public function regions() {
        return $this->has_many('Models\Region', 'map_id');
    }

    /**
     * Interface for json serialization define by JsonSerializable interface
     * will encode the whole map and all of it's children as a json object
     * @return string json representation of maps
     */
    public function jsonSerialize() {
        $data = $this->as_array();
        $data['regions'] = $this->regions()->find_many();
        return $data;
    }
}