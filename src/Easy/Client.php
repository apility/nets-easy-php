<?php

namespace NETS\Easy;

use NETS\Easy\Exceptions\BadRequestException;
use NETS\Easy\Exceptions\NotAuthorizedException;
use NETS\Easy\Exceptions\NotFoundException;
use NETS\Easy\Exceptions\PaymentException;
use Netflex\API\Client as API;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;

class Client extends API
{
  /** @var GuzzleClient */
  protected $client;

  /** @var string */
  protected $baseURI;

  /** @var string */
  protected $secretKey;

  /**
   * @param array $options
   */
  public function __construct($options = [])
  {
    $mode = ($options['mode'] ?? 'live');
    $domain = $options['domain'] ?? 'api.dibspayment.eu';
    $this->baseURI = $mode === 'test' ? ('https://test.' . $domain . '/v1/') : ('https://' . $domain . '/v1/');
    $this->secretKey = $options['secret_key'] ?? null;

    $options = [
      'base_uri' => $this->baseURI,
      'headers' => [
        'Authorization' => $this->secretKey
      ]
    ];

    $this->client = new Client($options);
  }

  /**
   * @param string $url
   * @param boolean $assoc = false
   * @return mixed
   * @throws Exception
   */
  public function get($url, $assoc = false)
  {
    try {
      return parent::get($url, $assoc);
    } catch (ClientException $e) {
      throw static::makeException($e);
    }
  }

  /**
   * @param string $url
   * @param array $payload = []
   * @param boolean $assoc = false
   * @return mixed
   * @throws Exception
   */
  public function put($url, $payload = [], $assoc = false)
  {
    try {
      return parent::put($url, $payload, $assoc);
    } catch (ClientException $e) {
      throw static::makeException($e);
    }
  }

  /**
   * @param string $url
   * @param array $payload = []
   * @param boolean $assoc = false
   * @return mixed
   * @throws Exception
   */
  public function post($url, $payload = [], $assoc = false)
  {
    try {
      return parent::post($url, $payload, $assoc);
    } catch (ClientException $e) {
      throw static::makeException($e);
    }
  }

  /**
   * @param string $url
   * @return mixed
   * @throws Exception
   */
  public function delete($url, $assoc = false)
  {
    try {
      return parent::delete($url, $assoc);
    } catch (ClientException $e) {
      throw static::makeException($e);
    }
  }

  /**
   * @param ClientException $e
   * @return PaymentException|ClientException
   */
  private static function makeException(ClientException $e)
  {
    $response = ($e->getResponse());

    switch ($response->getStatusCode()) {
      case 400:
        $errors = json_decode($response->getBody());
        return new BadRequestException(json_encode($errors->errors ?? $errors, JSON_PRETTY_PRINT));
      case 401:
        return new NotAuthorizedException('Invalid or missing credentials', 401);
      case 404:
        return new NotFoundException('Not found', 404);
      default:
        $body = json_decode($response->getBody());

        if ($body && isset($body->message)) {
          return new PaymentException($body->message, $response->getStatusCode());
        }

        return $e;
    }
  }
}
