<?php

namespace Nets\Easy;

/**
 * @property-read TextOptions $textOptions
 */
class Apperance extends EasyType
{
  /** @var array */
  protected $timestamps = [];

  public function getTextOptionsAttribute($textOptions)
  {
    return new TextOptions($textOptions);
  }
}
