<?php
namespace Libraries;

/**
 * Configuration
 *
 * This class loads a configuration from a json file stored somewhere
 * on the system which is then passed on to the actual application.
 * This allows us to handle multiple configurations for different
 * environments. This might by the local development environment, testing
 * or production.
 */
class Configuration implements \JsonSerializable {
	/**
	 * Holds the loaded data as an array which is then accessed using
	 * the magic get, set and isset methods
	 * @var array
	 */
	private $_data = array();

	public function __get($key) {
		return array_key_exists($key, $this->_data) ? $this->_data[$key] : null;
	}

	public function __set($key, $value) {
		$this->_data[$key] = $value;
	}

	public function __isset($key) {
		return array_key_exists($key, $this->_data);
	}

	/**
	 * Creates a new instance of the configuration, loading it's structure
	 * from the passed filename
	 * @param string $filename Config file to load
	 */
	public function __construct($filename) {
		if (file_exists($filename)) {
			// Load file and return instance
			$content     = file_get_contents($filename);
			$this->_data = json_decode($content, true);
			if ($this->_data == null) trigger_error('Configuration file could not be loaded or does not exist', E_ALL);
		} else {
			// Nothing loaded, trigger an error
			trigger_error('Configuration file could not be loaded or does not exist', E_ALL);
		}

		
	}

	/**
	 * Method implementation defined by interface JsonSerializable.
	 * Method is called in case the object is passed to a json_encode(...)
	 * call and will return the data structure which shall be encoded
	 * as json. WIll return a json representation of the configuration
	 * @return string Returns a serialized form of the configuration file
	 */
	public function jsonSerialize() {
		return $this->_data;
	}
}