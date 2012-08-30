<?php
namespace Models;

class Region extends Base {

	/**
	 * Defines the database table regions are stored in
	 * @var string
	 */
	public static $_table = 'regions';

	/**
	 * Retrieve the map this region is a part of
	 * @return Models\Map Returns the map instance this region belongs to
	 */
	public function map() {
		return $this->belongs_to('Models\Map', 'map_id');
	}

	/**
	 * Retrieve an array holding all districts bound to this region
	 * @return Array of Models\District An array of all bound districts
	 */
	public function districts() {
		return $this->has_many('Models\District', 'region_id');
	}

	/**
	 * Interface for json serialization define by JsonSerializable interface
	 * will encode the whole region and all of it's children as a json object
	 * @return string json representation of maps
	 */
	public function jsonSerialize() {
		$data = $this->as_array();
		$data['districts'] = $this->districts()->find_many();
		return $data;
    }
}