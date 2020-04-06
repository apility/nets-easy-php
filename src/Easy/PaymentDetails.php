<?php

namespace Nets\Easy;

/**
 * @property-read string $paymentType
 * @property-read string $paymentMethod
 * @property-read string $invoiceDetails
 * @property-read CardDetails $cardDetails
 */
class PaymentDetails extends EasyType
{
  /** @var array */
  protected $timestamps = [];

  public function getInvoiceDetailsAttribute($invoiceDetails)
  {
    return new InvoiceDetails($invoiceDetails);
  }

  public function getCardDetailsAttribute($cardDetails)
  {
    return new CardDetails($cardDetails);
  }
}
