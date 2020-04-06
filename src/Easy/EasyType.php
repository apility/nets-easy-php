<?php

namespace Nets\Easy;

use JsonSerializable;

abstract class EasyType implements JsonSerializable
{
  /** @var array */
  protected $attributes = [];

  /** @var array */
  public $modified = [];

  public function __construct($attributes = [])
  {
    $this->attributes = $attributes;
  }

  /**
   * @param string $str
   * @return string
   */
  public static function normalizeMethodName(string $str): string
  {
    return lcfirst(preg_replace_callback('/(^|[_ -])([a-z])/', function ($matches) {
      return strtoupper($matches[2]);
    }, $str));
  }

  private function getterName($property)
  {
    return str_replace('_', '', 'get' . ucfirst(static::normalizeMethodName($property)) . 'Attribute');
  }

  private function setterName($property)
  {
    return str_replace('_', '', 'set' . ucfirst(static::normalizeMethodName($property)) . 'Attribute');
  }

  /**
   * @param string $property
   * @return mixed
   */
  public function __get($property)
  {
    $value = $this->attributes[$property] ?? null;
    $getter = $this->getterName($property);

    if (method_exists($this, $getter)) {
      $value = $this->{$getter}($value);
    }

    if (property_exists($this, 'defaults')) {
      if (is_null($value) && array_key_exists($property, $this->defaults)) {
        $value = $this->defaults[$property];
      }
    }

    return $value;
  }

  /**
   * @param string $property
   * @param mixed $value
   */
  public function __set($property, $value)
  {
    if (
      !property_exists($this, 'readOnlyAttributes') ||
      !in_array($property, $this->readOnlyAttributes)
    ) {
      $setter = $this->setterName($property);

      if (method_exists($this, $setter)) {
        return $this->{$setter}($value);
      }

      $this->attributes[$property] = $value;
      $this->modified[] = $property;
      $this->modified = array_unique($this->modified);
    }
  }

  /**
   * @param string $property
   * @return bool
   */
  public function __isset($property)
  {
    return !is_null($this->{$property});
  }

  /**
   * @param string $key
   */
  public function __unset($key)
  {
    if (array_key_exists($key, $this->attributes)) {
      $this->__set($key, null);
    }
  }

  /**
   * @param string $property
   * @return bool
   */
  public function offsetExists($property)
  {
    $getter = $this->getterName($property);

    return method_exists($this, $getter) || array_key_exists($property, $this->attributes);
  }

  /**
   * @param string $property
   * @return mixed
   */
  public function offsetGet($property)
  {
    return $this->__get($property);
  }

  /**
   * @param string $property
   * @param mixed $value
   * @return mixed
   */
  public function offsetSet($property, $value)
  {
    $this->__set($property, $value);
  }

  /**
   * @param string $key
   * @return void
   */
  public function offsetUnset($key)
  {
    $this->__unset($key);
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return json_encode($this);
  }

  public function jsonSerialize()
  {
    if (!$this->attributes || empty($this->attributes) || empty(array_filter($this->attributes))) {
      return null;
    }

    return $this->__debugInfo();
  }

  public function __debugInfo()
  {
    return collect($this->attributes)
      ->keys()
      ->mapWithKeys(function ($key) {
        return [$key => $this->__get($key)];
      })->toArray();
  }
}
