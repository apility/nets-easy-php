<?php

namespace Apility\DIBS\Easy;

use Carbon\CarbonImmutable;

use Apility\DIBS\Easy;
use Apility\DIBS\Easy\Exceptions\ChargeException;
use Apility\DIBS\Easy\Exceptions\DIBSEasyException;
use Apility\DIBS\Easy\Exceptions\BadRequestException;
use Apility\DIBS\Easy\Exceptions\NotFoundException;
use Apility\DIBS\Easy\Exceptions\PaymentException;

/**
 * @property-read string $paymentId
 * @property-read mixed $summary
 * @property-read Consumber $consumer
 * @property-read PaymentDetails $paymentDetails
 * @property-read OrderDetails $orderDetails
 * @property-read Checkout $checkout
 * @property-read CarbonImmutable $created
 * @property-read Subscription $subscription
 */
class Payment extends DIBSEasyType
{
  /** @var array */
  protected $timestamps = [];

  public function getConsumerAttribute($consumer)
  {
    return new Consumer($consumer);
  }

  public function getPaymentDetailsAttribute($paymentDetails)
  {
    return new PaymentDetails($paymentDetails);
  }

  public function getOrderDetailsAttribute($orderDetails)
  {
    return new OrderDetails($orderDetails);
  }

  public function getCheckoutAttribute($checkout)
  {
    return new Checkout($checkout);
  }

  public function getCreatedAttribute($created)
  {
    return CarbonImmutable::parse($created);
  }

  public function getSubscriptionAttribute($subscription)
  {
    if ($subscription && isset($subscription['id'])) {
      return Subscription::retrieve($subscription['id']);
    }
  }

  /**
   * Undocumented function
   *
   * @param array $options
   * @throws DIBSEasyException
   * @return string Charge ID
   */
  public function charge($options = [])
  {
    try {
      return Easy::client()
        ->post('payments/' . $this->paymentId . '/charges', (object) $options);
    } catch (BadRequestException $e) {
      throw new ChargeException($e->getMessage(), $e->getCode());
    }
  }

  public static function retrieve($id)
  {
    try {
      $payment = Easy::client()->get('payments/' . $id, true)['payment'];
      return new static($payment);
    } catch (NotFoundException $e) {
      return null;
    }
  }

  public static function create($options = [])
  {
    try {
      return static::retrieve(
        Easy::client()
          ->post('payments', (object) $options)
          ->paymentId
      );
    } catch (BadRequestException $e) {
      throw new PaymentException($e->getMessage(), $e->getCode());
    }
  }

  /**
   * Generates the checkout form
   *
   * @param string $elementId ID of container element
   * @param array $options
   * @return string
   */
  public function form($options = [])
  {
    $options['checkoutKey'] = $options['checkoutKey'] ?? Easy::checkoutKey();
    $options['paymentId'] = $options['paymentId'] ?? $this->paymentId;
    $options['partnerMerchantNumber'] = $options['partnerMerchantNumber'] ?? Easy::merchantId();
    $options['containerId'] = $options['containerId'] ?? 'dibs-complete-checkout';

    $redirect = $options['redirect'] ?? '';
    $redirect = rtrim($redirect, '?');

    if ($redirect) {
      unset($options['redirect']);
    }

    $src = Easy::checkoutSrc();
    $checkoutOptions = json_encode((object) $options);

    return <<<JS
      <script>
        (function () {
          var options = $checkoutOptions;
          if (!document.getElementById(options.containerId)) {
            var container = document.createElement('div')
            container.id = options.containerId
            document.body.appendChild(container)
          }
          var script = document.createElement('script');
          script.addEventListener('load', function () {
            var checkout = new Dibs.Checkout(options);
            checkout.on('payment-completed', function(response) {
              window.location = '$redirect?paymentId=' + response.paymentId;
            });
          });
          script.src = '$src';
          document.body.appendChild(script)
        })();
      </script>
JS;
  }
}
