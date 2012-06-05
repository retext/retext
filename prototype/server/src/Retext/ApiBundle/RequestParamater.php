<?php

namespace Retext\ApiBundle;

class RequestParamater
{
    const FORMAT_STRING = 1;
    const FORMAT_INTEGER = 2;
    const FORMAT_LIST = 3;

    /**
     * @var bool
     */
    private $required = true;

    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $defaultValue = null;

    /**
     * @var string
     */
    private $format = self::FORMAT_STRING;

    /**
     * @static
     * @param $name
     * @return \Retext\ApiBundle\RequestParamater
     */
    public static function create($name)
    {
        return new RequestParamater($name);
    }

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @return \Retext\ApiBundle\RequestParamater
     */
    public function makeOptional()
    {
        $this->required = false;
        return $this;
    }

    /**
     * @param mixed $value
     * @return \Retext\ApiBundle\RequestParamater
     */
    public function defaultsTo($value)
    {
        $this->defaultValue = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return \Retext\ApiBundle\RequestParamater
     */
    public function makeInteger()
    {
        $this->format = self::FORMAT_INTEGER;
        return $this;
    }

    /**
     * @return \Retext\ApiBundle\RequestParamater
     */
    public function makeList()
    {
        $this->format = self::FORMAT_LIST;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }
}
