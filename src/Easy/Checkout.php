<?php

namespace Apility\DIBS\Easy;

/**
 * @property-read int $url
 */
class Checkout extends DIBSEasyType
{
  /** @var array */
  protected $timestamps = [];

  const INTEGRATION_TYPE_HOSTED = 'HostedPaymentPage';
  const INTEGRATION_TYPE_EMBEDDED = 'EmbeddedCheckout';
}
