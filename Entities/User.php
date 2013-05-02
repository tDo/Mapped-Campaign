<?php
namespace Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table(name="users")
 */
class User {
    /**
     * User ID
     * 
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     **/
    protected $id;

    /** 
     * Username
     * 
     * @Column(type="string") 
     * @var string
     **/
    protected $name;

    /** 
     * User password (Make sure to not store this in plain text)
     * 
     * @Column(type="string") 
     * @var string
     **/
    protected $password;

    public function __construct() {
    }

    /**
     * Get the userid as stored in the database
     * @return int Id of the entity
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get the username
     * @return string Name of the entity
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set the username
     * @param string $name Username to assign
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Get the username
     * @return string Username
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set the username
     * @param string $name Password to store
     */
    public function setPassword($password) {
        $this->password = $password;
    }
}