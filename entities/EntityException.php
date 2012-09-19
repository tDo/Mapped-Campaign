<?php
namespace Entities;

class EntityException extends \Exception {
    protected $statusCode = 200;
    protected $invalidFields   = array();

    protected function addInvalidFields($fields) {
        if (is_string($fields))
            $this->invalidFields[] = $fields;
        else if (is_array($fields))
            foreach ($fields as $value)
                $this->addInvalidFields($value);
    }

    public function getInvalidFields() {
        return $this->invalidFields;
    }

    public function getStatusCode() {
        return $this->statusCode;
    }

    public function __construct($message, $invalidFields, $statusCode = 200) {
        parent::__construct($message);
        $this->addInvalidFields($invalidFields);
        $this->statusCode = $statusCode;
    }
}