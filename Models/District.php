<?php
namespace Models;

class District extends Base {
	/**
	 * Defines the database table districts are stored in
	 * @var string
	 */
	public static $_table = 'districts';

	/**
	 * Retrieve the region this district belongs to
	 * @return Models\Region Instance of the region the district belongs to
	 */
	public function region() {
		return $this->belongs_to('Models\Region', 'region_id');
	}

	/**
	 * Retrieve a list of all points of interest in this district
	 * @return Array of Models\PointOfInterest List holding all points of interest for this district
	 */
	public function pointsOfInterest() {
		return $this->has_many('Models\PointOfInterest', 'district_id');
	}

	/**
	 * Retrieve a list of all locations in this district
	 * @return Array of Models\Location List of all locations in this district
	 */
	public function locations() {
		return $this->has_many('Models\Location', 'district_id');
	}

	/**
	 * Retrieve a list of all buildings in this district (worth noting)
	 * @return Array of Models\Building A list of all buildings in this district
	 */
	public function buildings() {
		return $this->has_many('Models\Building', 'district_id');
	}

	/**
	 * Interface for json serialization define by JsonSerializable interface
	 * will encode the whole district and all of it's children as a json object
	 * @return string json representation of maps
	 */
	public function jsonSerialize() {
		$data = $this->as_array();
		$data['pointsOfInterest']   = $this->pointsOfInterest()->find_many();
		$data['locations']          = $this->locations()->find_many();
		$data['buildings']          = $this->buildings()->find_many();
		return $data;
    }
}