<?php

namespace Vluzrmos\SlackApi;

use GuzzleHttp\Client;
use Illuminate\Support\Traits\Macroable;

class SlackApi {
  use Macroable;

  /**
   *
   * @var \GuzzleHttp\Client
   */
  private $client;

  /**
   * Token of the user of the Slack team (with administrator levels)
   * @var string
   */
  private $token;

  /**
   * Url to slack.com, by default will use https://slack.com/api
   * @var String
   */
  private $url = "https://slack.com/api";

  function __construct(Client $client, $token=null){
    $this->setClient($client);
    $this->setToken($token);
  }

  /**
   * @param string $method
   * @param string $url
   * @param array $parameters
   * @return mixed;
   */
  public function method($method = "get", $url="", $parameters = []){
    return json_decode(($this->getClient()->$method($url, $parameters)->getBody()->getContents()), true);
  }

  /**
   * Send a GET Request
   * @param $apiMethod
   * @param array $parameters
   * @return \GuzzleHttp\Message\ResponseInterface
   */
  public function get($apiMethod, $parameters = []){
    $url = $this->getUrl($apiMethod);
    $parameters = $this->mergeParameters($parameters);

    return $this->method('get', $url, $parameters);
  }

  /**
   * Send a POST Request
   * @param $apiMethod
   * @param array $parameters
   * @return \GuzzleHttp\Message\ResponseInterface
   */
  public function post($apiMethod, $parameters = []){
    $url = $this->getUrl($apiMethod);
    $parameters = $this->mergeParameters($parameters);

    return $this->method('post', $url, $parameters);
  }

  /**
   * Send a PUT Request
   * @param $apiMethod
   * @param array $parameters
   * @return \GuzzleHttp\Message\ResponseInterface
   */
  public function put($apiMethod, $parameters = []){
    $url = $this->getUrl($apiMethod);
    $parameters = $this->mergeParameters($parameters);

    return $this->method('put', $url, $parameters);
  }

  /**
   * Send a DELETE Request
   * @param $apiMethod
   * @param array $parameters
   * @return \GuzzleHttp\Message\ResponseInterface
   */
  public function delete($apiMethod, $parameters = []){
    $url = $this->getUrl($apiMethod);
    $parameters = $this->mergeParameters($parameters);

    return $this->method('delete', $url, $parameters);
  }

  /**
   * Send a PATCH Request
   * @param $apiMethod
   * @param array $parameters
   * @return \GuzzleHttp\Message\ResponseInterface
   */
  public function patch($apiMethod, $parameters = []){
    $url = $this->getUrl($apiMethod);
    $parameters = $this->mergeParameters($parameters);

    return $this->method('patch', $url, $parameters);
  }

  /**
   * Generate the url with the api $method.
   * @param null $method
   * @return string
   */
  protected function getUrl($method=null){
    return str_finish($this->url, "/").$method;
  }

  /**
   * Get the user token
   * @return null|string
   */
  protected function getToken(){
    return $this->token;
  }

  /**
   * Set the token of your slack team member (be sure is admin token)
   * @param $token
   */
  public function setToken($token){
    $this->token = $token;
  }

  /**
   * Configures the Guzzle Client
   * @param \GuzzleHttp\Client|Callback|null $client
   * @param String $verify SSL cert used for HTTPS requests
   */
  public function setClient($client = null, $verify=null){
    if(is_callable($client)){
      $this->client = value($client);
    }
    elseif(is_null($this->client)){
      if(is_null($client)){
        $this->client = new Client();
      }
      else{
        $this->client = $client;
      }
    }

    $this->setSSLVerfyPath($verify);
  }

  /**
   * Configures the path to SSL Cert used on every HTTPS request
   * @param String|null $path
   */
  public function setSSLVerfyPath($path = null){
    $this->getClient()->setDefaultOption('verify', empty($path)?$this->getSSLVerifyPath():$path);
  }

  /**
   * Get the SSL cert used on every HTTPS request
   * @return string
   */
  public function getSSLVerifyPath(){
    return $this->client->getDefaultOption('verify')?:realpath(__DIR__."../../../ssl/curl-ca-bundle.crt");
  }
  /**
   * Merge parameters of the request with token em timestamp string
   * @param $parameters
   * @return mixed
   */
  protected function mergeParameters($parameters){
    $parameters['query'] = array_merge([
        't' => time(),
        'token' => $this->getToken()
    ], array_get($parameters, 'query', []));

    $parameters['body'] = array_get($parameters, 'body');

    return $parameters;
  }

  public function getClient(){
    return $this->client;
  }
}