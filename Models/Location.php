<?php
namespace Models;

class Location extends Base {
    /**
     * Defines the database table locations are stored in
     * @var string
     */
    public static $_table = 'locations';

    /**
     * Retrieves the district this location is located in
     * @return Models\District Instance of the district this location is located in
     */
    public function district() {
        return $this->belongs_to('Models\District', 'district_id');
    }
}