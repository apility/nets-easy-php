<?php

namespace Nets\Easy;

/**
 * @property-read int $amount
 * @property-read string $currency
 * @property-read string $reference
 */
class OrderDetails extends EasyType
{
  /** @var array */
  protected $timestamps = [];
}
