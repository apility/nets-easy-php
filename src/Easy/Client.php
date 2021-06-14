<?php

namespace Nets\Easy;

use Psr\Http\Message\ResponseInterface;

use Nets\Easy\Exceptions\BadRequestException;
use Nets\Easy\Exceptions\NotAuthorizedException;
use Nets\Easy\Exceptions\NotFoundException;
use Nets\Easy\Exceptions\PaymentException;
use Nets\Easy\Exceptions\ServerException;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;

class Client
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

    $this->client = new GuzzleClient($options);
  }

  /**
   * @param string $url
   * @return mixed
   * @throws Exception
   */
  public function get($url)
  {
    try {
      return $this->parseResponse($this->client->get($url));
    } catch (BadResponseException $e) {
      throw static::makeException($e);
    }
  }

  /**
   * @param string $url
   * @param array $payload = []
   * @return mixed
   * @throws Exception
   */
  public function put($url, $payload = [])
  {
    try {
      return $this->parseResponse($this->client->put($url, ['json' => $payload]));
    } catch (BadResponseException $e) {
      throw static::makeException($e);
    }
  }

  /**
   * @param string $url
   * @param array $payload = []
   * @return mixed
   * @throws Exception
   */
  public function post($url, $payload = [])
  {
    try {
      return $this->parseResponse($this->client->post($url, ['json' => $payload]));
    } catch (BadResponseException $e) {
      throw static::makeException($e);
    }
  }

  /**
   * @param string $url
   * @return mixed
   * @throws Exception
   */
  public function delete($url)
  {
    try {
      return $this->parseResponse($this->client->delete($url));
    } catch (BadResponseException $e) {
      throw static::makeException($e);
    }
  }

  /**
   * @param BadResponseException $e
   * @return PaymentException|BadResponseException
   */
  private static function makeException(BadResponseException $e)
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
      case 500:
        $parsedResponse = trim(strip_tags((string) $response->getBody()));
        return new ServerException('Server Exception' . ($parsedResponse ? (': ' . $parsedResponse) : null), 500);
      default:
        $body = json_decode($response->getBody());

        if ($body && isset($body->message)) {
          return new PaymentException($body->message, $response->getStatusCode());
        }

        return $e;
    }
  }

  /**
   * @param ResponseInterface $response
   * @return mixed
   */
  protected function parseResponse(ResponseInterface $response)
  {
    $body = $response->getBody();

    $contentType = strtolower($response->getHeaderLine('Content-Type'));

    if (strpos($contentType, 'json') !== false) {
      $jsonBody = json_decode($body, true);

      if (json_last_error() === JSON_ERROR_NONE) {
        return $jsonBody;
      }
    }

    if (strpos($contentType, 'text') !== false) {
      return $body->getContents();
    }

    return null;
  }
}
