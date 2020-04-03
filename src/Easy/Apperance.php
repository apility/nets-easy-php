<?php

namespace Apility\DIBS\Easy;

/**
 * @property-read TextOptions $textOptions
 */
class Apperance extends DIBSEasyType
{
  /** @var array */
  protected $timestamps = [];

  public function getTextOptionsAttribute($textOptions)
  {
    return new TextOptions($textOptions);
  }
}
