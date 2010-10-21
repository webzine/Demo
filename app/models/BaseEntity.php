<?php
/**
 * Demo - knihovní systém
 *
 * @package    Models
 */

namespace Models;

use \InvalidArgumentException;
use Nette\Object;

/**
 * Základní entita.
 *
 * @MappedSuperclass
 * @HasLifecycleCallbacks
 *
 * @author     Tomáš Penc
 * @package    Models
 *
 * @property-read int $id
 */
class BaseEntity extends Object implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @Id
     * @Column(type = "integer")
     * @GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @return int
     */
    final public function getId()
    {
        return $this->id;
    }

    public function __construct($data = null)
    {
        if(!is_null($data)) {
            $this->loadValues($data);
        }
    }

    /**
     * Načte data z pole do entity.
     *
     * @param mixed $data
     * @return void
     */
    public function loadValues($data)
    {
        foreach ($data as $key => $value) {
            if($this->__isset($key))
                $this->__set($key, $value);
        }
    }

    /**
     * Vrací všechny hodnoty entity.
     *
     * @return array
     */
    public function getValues()
    {
        $values = array();
        foreach($this->reflection->getProperties() as $property) {
            $values[$property->name] = $this->{$property->name};
        }
        return $values;
    }

    // <editor-fold defaultstate="collapsed" desc="Events">
    /** @PreRemove */
    public function onPreRemove(){}
    
    /** @PostRemove */
    public function onPostRemove(){}

    /** @PrePersist */
    public function onPrePersist(){}

    /** @PostPersist */
    public function onPostPersist(){}

    /** @PreUpdate */
    public function onPreUpdate(){}

    /** @PostUpdate */
    public function onPostUpdate(){}

    /** @PostLoad */
    public function onPostLoad(){}
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="ArrayAccess">
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="IteratorAggregate">
    public function getIterator()
    {
        return new \ArrayIterator($this->getValues());
    }
    // </editor-fold>

}
