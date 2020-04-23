<?php

namespace Nets\Easy;

use Nets\Easy;
use Nets\Easy\Exceptions\PaymentException;
use Nets\Easy\Exceptions\NotFoundException;
use Nets\Easy\EasyType;
use Nets\Easy\Exceptions\SubscriptionException;
use Nets\Easy\PaymentDetails;

use Carbon\Carbon;

/**
 * @property-read string $subscriptionId
 * @property-read int $frequency
 * @property-read int $interval
 * @property-read Carbon $endDate
 * @property-read PaymentDetails $paymentDetails
 */
class Subscription extends EasyType
{
  /** @var array */
  protected $timestamps = [];

  public function getEndDateAttribute($endDate)
  {
    return Carbon::parse($endDate);
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
   * @see \Nets\Easy\Payment::create
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

  /**
   * Charges the subscription
   *
   * @param array $options
   * @return Payment
   * @throws PaymentException If the card is rejected
   */
  public function charge($options = [])
  {
    $response = Easy::client()
      ->post('subscriptions/' . $this->subscriptionId . '/charges', $options);

    return Payment::retrieve($response['paymentId']);
  }
}
