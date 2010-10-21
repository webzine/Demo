<?php
/**
 * Demo - knihovní systém
 *
 * @package    Models
 */

namespace Models;

/**
 * Model Book.
 *
 * @Entity
 * @table(name="book")
 *
 * @author     Tomáš Penc
 * @package    Models
 *
 * @property string $name
 * @property string $author
 * @property int $state
 * @property-read string $stateName
 * @property string $stateDescription
 */
class Book extends BaseEntity
{
    /** Stavy knihy */
    const STATE_FREE = 1;
    const STATE_LENT = 2;
    
    /**
     * @column(length=255)
     * @var string
     */
    private $name;

    /** @return string */
    public function getName() {
        return $this->name;
    }

    /** @param string $value */
    public function setName($value) {
        $this->name = $value;
    }

    /**
     * @column(length=255)
     * @var string
     */
    private $author;

    /** @return string */
    public function getAuthor() {
        return $this->author;
    }

    /** @param string $value */
    public function setAuthor($value) {
        $this->author = $value;
    }

    /**
     * @column(type="smallint")
     * @var int
     */
    private $state = self::STATE_FREE;

    /** @return int */
    public function getState() {
        return $this->state;
    }

    /** @return string */
    public function getStateName() {
        return $this->state == self::STATE_FREE ? "volná" : "půjčená";
    }

    /** @param int $value */
    public function setState($value) {
        if($value != self::STATE_FREE && $value != self::STATE_LENT)
            throw new \InvalidArgumentException("Allowed value for state property is STATE_FREE or STATE_LENT.");
        $this->state = $value;
    }

    /**
     * @column(type="text",nullable=true)
     * @var string
     */
    private $stateDescription;

    /** @return string */
    public function getStateDescription() {
        return $this->stateDescription;
    }

    /** @param string $value */
    public function setStateDescription($value) {
        $this->stateDescription = $value;
    }
}