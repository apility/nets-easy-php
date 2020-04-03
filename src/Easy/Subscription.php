<?php

namespace Apility\DIBS\Easy;

use Apility\DIBS\Easy;
use Apility\DIBS\Easy\Exceptions\PaymentException;
use Apility\DIBS\Easy\Exceptions\NotFoundException;
use Apility\DIBS\Easy\DIBSEasyType;
use Apility\DIBS\Easy\Exceptions\BadRequestException;
use Apility\DIBS\Easy\Exceptions\SubscriptionException;
use Apility\DIBS\Easy\PaymentDetails;

use Carbon\CarbonImmutable;

/**
 * @property-read string $subscriptionId
 * @property-read int $frequency
 * @property-read int $interval
 * @property-read CarbonImmutable $endDate
 * @property-read PaymentDetails $paymentDetails
 */
class Subscription extends DIBSEasyType
{
  /** @var array */
  protected $timestamps = [];

  public function getEndDateAttribute($endDate)
  {
    return CarbonImmutable::parse($endDate);
  }

  public function getPaymentDetailsAttribute($paymentDetails)
  {
    return new PaymentDetails($paymentDetails);
  }

  public static function retrieve($id)
  {
    try {
      $subscription = Easy::client()->get('subscriptions/' . $id, true);
      return new static($subscription);
    } catch (NotFoundException $e) {
      return null;
    }
  }

  /**
   * @param array $options
   * @return Payment
   * @see \Apility\DIBS\Easy\Payment::create
   */
  public static function create($options = [])
  {
    if (!($options['subscription'] ?? null)) {
      throw new SubscriptionException(json_encode([
        'subscription' => 'Subscription object is required'
      ], JSON_PRETTY_PRINT));
    }

    try {
      return Payment::create($options);
    } catch (PaymentException $e) {
      throw new SubscriptionException($e->getMessage(), $e->getCode());
    }
  }
}
