<?php
namespace Models;

abstract class Base extends \Model implements \JsonSerializable {
	
	public function jsonSerialize() {
        return $this->as_array();
    }
}