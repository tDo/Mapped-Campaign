<?php
namespace Models;

class PointOfInterest extends Base {
	/**
	 * Defines the database table points of interest are stored in
	 * @var string
	 */
	public static $_table = 'points_of_interest';

	/**
	 * Retrieves the district this point of interest is located in
	 * @return Models\District Instance of the district this point of interest is located in
	 */
	public function district() {
		return $this->belongs_to('Models\District', 'district_id');
	}
}