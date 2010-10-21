<?php
/**
 * Demo - knihovní systém
 *
 * @package    Models
 */

namespace Models;

/**
 * Model User.
 *
 * @Entity
 * @table(name="user")
 *
 * @author     Tomáš Penc
 * @package    Models
 *
 * @property string $username
 * @property string $email
 * @property string $password
 */
class User extends BaseEntity
{
    /**
     * @column(length=255,unique=true)
     * @var string
     */
    private $username;

    /** @return string */
    public function getUsername() {
        return $this->username;
    }

    /** @param string $value */
    public function setUsername($value) {
        $this->username = $value;
    }

    /**
     * @column(length=255,unique=true)
     * @var string
     */
    private $email;

    /** @return string */
    public function getEmail() {
        return $this->email;
    }

    /** @param string $value */
    public function setEmail($value) {
        $this->email = $value;
    }
    
    /**
     * @column(length=255)
     * @var string
     */
    private $password;

    /** @return string */
    public function getPassword() {
        return $this->password;
    }

    /** @param string $name */
    public function setPassword($value) {
        $this->password = sha1($value);
    }

    /**
     * Ověří heslo.
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password)
    {
        return sha1($password) == $this->password;
    }
}