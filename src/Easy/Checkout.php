<?php

namespace Nets\Easy;

/**
 * @property-read int $url
 */
class Checkout extends EasyType
{
  /** @var array */
  protected $timestamps = [];

  const INTEGRATION_TYPE_HOSTED = 'HostedPaymentPage';
  const INTEGRATION_TYPE_EMBEDDED = 'EmbeddedCheckout';
}
