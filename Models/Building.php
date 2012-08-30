<?php
namespace Models;

class Building extends Base {
    /**
     * Defines the database table buildings are stored in
     * @var string
     */
    public static $_table = 'buildings';

    /**
     * Retrieves the district this bulding is located in
     * @return Models\District Instance of the district this building is located in
     */
    public function district() {
        return $this->belongs_to('Models\District', 'district_id');
    }
}