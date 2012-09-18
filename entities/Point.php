<?php
namespace Entities;


/**
 * A base point location which has a single x-y based location
 * stored for
 */
abstract class Point extends Base {

    /** 
     * @Column(type="integer")
     * @var int
     **/
    protected $x;
    /** 
     * @Column(type="integer")
     * @var int
     **/
    protected $y;

    public function getX() {
        return $this->x;
    }
    public function setX($x) {
        $this->x = $x;
    }

    public function getY() {
        return $this->y;
    }
    public function setY($y) {
        $this->y = $y;
    }
}