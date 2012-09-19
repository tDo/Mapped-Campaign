<?php
namespace Entities;

/**
 * A base point location which has a single x-y based location
 * stored for
 */
abstract class Point extends Base {

    /** 
     * X-Position on the map
     * @Column(type="integer")
     * @var int
     **/
    protected $x;
    /** 
     * Y-Position on the map
     * @Column(type="integer")
     * @var int
     **/
    protected $y;

    /**
     * Retrieve the X-Position of this entity on the map
     * @return float X-Position on the map
     */
    public function getX() {
        return $this->x;
    }
    /**
     * Set the X-Position of this entity on the map
     * @param float $x X-Position on the map
     */
    public function setX($x) {
        $this->x = $x;
    }

    /**
     * Retrieve the Y-Position of this entity on the map
     * @return float Y-Position on the map
     */
    public function getY() {
        return $this->y;
    }
    /**
     * Set the Y-Position of this entity on the map
     * @param float $y Y-Position on the map
     */
    public function setY($y) {
        $this->y = $y;
    }
}