<?php

namespace Nets;

use Nets\Easy\Client;

class Easy
{
  protected static $apiClient;

  /** @var string */
  protected static $merchantId;

  /** @var string */
  protected static $secretKey;

  /** @var string */
  protected static $checkoutKey;

  /** @var string */
  protected static $mode;

  /** @var string */
  protected static $domain;

  protected static $commercePlatformTag = null;

  const MODE_LIVE = 'live';
  const MODE_TEST = 'test';

  /**
   * @param array $options
   * @return void
   */
  public static function setup($options = [])
  {
    static::$merchantId = $options['merchant_id'] ?? null;
    static::$secretKey = $options['secret_key'] ?? null;
    static::$checkoutKey = $options['checkout_key'] ?? null;
    static::$mode = $options['mode'] ?? 'live';
    static::$domain = $options['domain'] ?? null;
  }

  public static function setCommercePlatformTag($tag)
  {
    static::$commercePlatformTag = $tag;
  }

  /**
   * @return Client
   */
  public static function client()
  {
    if (!static::$apiClient) {
      static::$apiClient = new Client([
        'mode' => static::$mode,
        'domain' => static::$domain,
        'secret_key' => static::$secretKey ?? null
      ]);
    }

    if (static::$commercePlatformTag) {
      static::$apiClient->addHeader('commercePlatformTag', static::$commercePlatformTag);
    }

    return static::$apiClient;
  }

  /**
   * @return string|null
   */
  public static function merchantId()
  {
    return static::$merchantId;
  }

  /**
   * @return string|null
   */
  public static function checkoutKey()
  {
    return static::$checkoutKey;
  }

  public static function mode()
  {
    return static::$mode === static::MODE_TEST ? static::MODE_TEST : static::MODE_LIVE;
  }

  public static function checkoutSrc()
  {
    if (static::mode() === static::MODE_TEST) {
      return 'https://test.checkout.dibspayment.eu/v1/checkout.js?v=1';
    }

    return 'https://checkout.dibspayment.eu/v1/checkout.js?v=1';
  }
}
