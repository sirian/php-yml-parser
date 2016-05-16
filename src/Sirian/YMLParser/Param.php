<?php
/**
 * Project: biglion
 * User: Ivan Koretskiy <i.koretskiy@biglion.ru>
 * Date: 14/01/15
 * Time: 15:50
 */

namespace Sirian\YMLParser;


/**
 * Class Param
 * @author Ivan Koretskiy <gillbeits@gmail.com>
 * @package Sirian\YMLParser
 */
class Param {

    protected $name;
    protected $unit;
    protected $value;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param mixed $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }


}