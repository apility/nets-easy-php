<?php

namespace Apility\DIBS\Easy;

/**
 * @property-read int $amount
 * @property-read string $currency
 * @property-read string $reference
 */
class OrderDetails extends DIBSEasyType
{
  /** @var array */
  protected $timestamps = [];
}
