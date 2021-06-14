# Nets Easy PHP bindings

[![CircleCI](https://circleci.com/gh/apility/nets-easy-php.svg?style=shield&circle-token=d878cbbe3e98c96ba07f7baaec7cf7fd11bd2399)](https://circleci.com/gh/apility/nets-easy-php)
[![License](https://img.shields.io/badge/license-MIT-brightgreen)](https://github.com/apility/nets-easy-php/blob/master/LICENSE)

The NETS Easy PHP library provides convenient access to the NETS Easy API from
applications written in the PHP language. It includes a pre-defined set of
classes for API resources that initialize themselves dynamically from API
responses.

## Requirements

PHP 7.3.0 and later.

## Composer

You can install the bindings via [Composer](http://getcomposer.org/). Run the following command:

```bash
composer require apility/nets-easy
```

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once('vendor/autoload.php');
```

##

## Getting Started

Simple usage looks like:

### Backend integration

```php
<?php

use Nets\Easy;
use Nets\Easy\Payment;

Easy::setup([
  'secret_key' => '00000000000000000000000000000000',
  'checkout_key' => '00000000000000000000000000000000',
  'merchant_id' => '000000000'
]);

$payment = Payment::create([
  'checkout' => [
    'url' => 'https://domain.tld/checkout',   // The exact URL where your checkout is hosted (except query parameters and fragments)
    'termsUrl' => 'https://domain.tld/terms', // Your terms
  ],
  'order' => [
    'currency' => 'NOK',
    'reference' => '1',            // A unique reference for this specific order
    'amount' => 10000,              // Total order amount in cents
    'items' => [
      [
        'reference' => '1',        // A unique reference for this specific item
        'name' => 'Test',
        'quantity' => 1,
        'unit' => 'pcs',
        'unitPrice' => 8000,        // Price per unit except tax in cents
        'taxRate' => 25000          // Taxrate (e.g 25.0 in this case),
        'taxAmount' => 2000         // The total tax amount for this item in cents,
        'grossTotalAmount' => 10000 // Total for this item with tax in cents,
        'netTotalAmount' => 8000    // Total for this item without tax in cents
      ]
    ]
  ]
]);
```

### Frontend integration

The simplest way to integrate the checkout form in the frontend, is to use the `form` helper method on the Payment object.

```php
<?php

use Nets\Easy;
use Nets\Easy\Payment;

Easy::setCredentials(...);

$payment = isset($_GET['paymentId']
  ? Payment::retrieve($_GET['paymentId'])
  : Payment::create(...);

$form = $payment->form([
  'redirect' => 'https://domain.tld/checkout/complete', // URL to redirect to on payment completion (paymentId query parameter is appended)
  'theme' => [ // Optional
    'textColor' => 'blue',
    'linkColor' => '#bada55',
    'buttonRadius' => '50px'
  ]
]);

?>
<div id="easy-complete-checkout"></div>
<?php echo $form; ?>
```

Alternatively, if you require full control of checkout flow, implement it manually:

```html
<html>
  <head>
    <title>Example checkout</title>
  </head>
  <body>
    <div id="easy-complete-checkout"></div>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const checkout = new Dibs.Checkout({
          checkoutKey: "00000000000000000000000000000000", // Checkout Key
          paymentId: "00000000000000000000000000000000", // Payment ID created in the backend integration
          partnerMerchantNumber: "000000000", // Merchant ID
          containerId: "easy-complete-checkout", // Container element where the checkout form should be created
        })

        checkout.on('payment-completed', ({ paymentId }) => {
          // Payment is completed, do what you need to do here
        })
      })
    </script>
    <script src="https://test.checkout.dibspayment.eu/v1/checkout.js?v=1"></script>
    <!-- or if in production: -->
    <script src="https://checkout.dibspayment.eu/v1/checkout.js?v=1"></script>  </body>
</html>
```

## Documentation

See the [Official API docs](https://tech.dibspayment.com/easy/api).
