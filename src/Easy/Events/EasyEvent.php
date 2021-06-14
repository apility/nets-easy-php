<?php

namespace Nets\Easy\Events;

use Nets\Easy\Payment;
use Netflex\Commerce\Order;

use Illuminate\Queue\SerializesModels;

class EasyEvent
{
  use SerializesModels;

  /** @var Payment */
  public $payment;

  const PAYMENT_CREATED = 'payment.created';
  const PAYMENT_RESERVATION_CREATED = 'payment.reservation.created';
  const PAYMENT_RESERVATION_CREATED_V2 = 'payment.reservation.created.v2';
  const PAYMENT_CHECKOUT_COMPLETED = 'payment.checkout.completed';
  const PAYMENT_CHARGE_CREATED = 'payment.charge.created';
  const PAYMENT_CHARGE_CREATED_V2 = 'payment.charge.created.v2';
  const PAYMENT_CHARGE_FAILED = 'payment.charge.failed';
  const PAYMENT_REFUND_INITIATED = 'payment.refund.initiated';
  const PAYMENT_REFUND_INITIATED_V2 = 'payment.refund.initiated.v2';
  const PAYMENT_REFUND_FAILED = 'payment.refund.failed';
  const PAYMENT_REFUND_COMPLETED = 'payment.refund.completed';
  const PAYMENT_CANCEL_CREATED = 'payment.cancel.created';
  const PAYMENT_CANCEL_FAILED = 'payment.cancel.failed';

  const EVENTS = [
    'payment.created' => PaymentCreated::class,
    'payment.reservation.created' => PaymentReservationCreated::class,
    'payment.reservation.created.v2' => PaymentReservationCreatedV2::class,
    'payment.checkout.completed' => PaymentCheckoutCompleted::class,
    'payment.charge.created' => PaymentChargeCreated::class,
    'payment.charge.created.v2' => PaymentChargeCreatedV2::class,
    'payment.charge.failed' => PaymentChargeFailed::class,
    'payment.refund.initiated' => PaymentRefundInitiated::class,
    'payment.refund.initiated.v2' => PaymentRefundInitiatedV2::class,
    'payment.refund.failed' => PaymentRefundFailed::class,
    'payment.refund.completed' => PaymentRefundCompleted::class,
    'payment.cancel.created' => PaymentCancelCreated::class,
    'payment.cancel.failed' => PaymentCancelFailed::class
  ];

  /**
   * Create a new event instance.
   *
   * @param Order $order
   * @param Payment $payment
   * @return void
   */
  public function __construct(Payment $payment)
  {
    $this->payment = $payment;
  }
}
