<?php

namespace NETS\Easy;

use JsonSerializable;

use Netflex\Support\Accessors;

abstract class EasyType implements JsonSerializable
{
  use Accessors;

  public function __construct($attributes = [])
  {
    $this->attributes = $attributes;
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
