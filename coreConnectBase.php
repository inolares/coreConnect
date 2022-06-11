<?php declare(strict_types=1);
/**
 * Base class provides all InoCore communication methods.
 * @package coreConnect
 * @author Sascha 'SieGeL' Pfalz <s.pfalz@inolares.de>
 * @copyright Inolares GmbH & Co. KG
 * @version 2.0.0 (11-Jun-2022)
 * @license BSD
 */

namespace inolares;

use BadMethodCallException;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use InvalidArgumentException;

/**
 * Base class for REST API communication to InoCore
 */
abstract class coreConnectBase
  {
  /** @var string Class version */
  const CLASS_VERSION = '2.0.0';
  
  /**
   * Sets API url
   * @param string $url
   * @return void
   */
  abstract protected function setApiUrl(string $url): void;

  /**
   * Returns API url
   * @return string
   */
  abstract protected function getApiUrl():string;
  
  /**
   * Sets API username
   * @param string $user
   * @return void
   */
  abstract protected function setApiUser(string $user):void;
 
  /**
   * Returns API username
   * @return string
   */
  abstract protected function getApiUser():string;
  
  /**
   * Set API password
   * @param string $pass
   * @return void
   */
  abstract protected function setApiPass(string $pass):void;
  
  /**
   * Returns API password
   * @return string
   */
  abstract protected function getApiPass():string;
  
  /**
   * Set Expires timestamp
   * @param int $timestamp
   * @return void
   */
  abstract protected function setExpires(int $timestamp):void;
  
  /**
   * Returns timestamp
   * @return int
   */
  abstract protected function getExpires():int;
  
  /**
   * Sets token
   * @param string $token
   * @return void
   */
  abstract protected function setToken(string $token):void;
  
  /**
   * Returns token
   * @return string
   */
  abstract protected function getToken():string;
  
  /**
   * Set JWT user data array as json_encoded() string
   * @param string $json_data
   * @return void
   */
  abstract protected function setJwtUser(string $json_data):void;
  
  /**
   * Returns json encoded string of JWT user data array.
   * @return string
   */
  abstract protected function getJwtUser():string;
  
  /** @var string User agent to send to server */
  const USER_AGENT = 'coreConnect/'.self::CLASS_VERSION;
  
  /** @var false|resource|null curl instance */
  protected $curl = null;
  
  /** Supported HTTP methods */
  const METHOD_GET    = 'GET';
  const METHOD_PUT    = 'PUT';
  const METHOD_POST   = 'POST';
  const METHOD_DELETE = 'DELETE';
  
  /** @var array $validMethods Array of valid HTTP methods */
  protected array $validMethods = [
    self::METHOD_GET,
    self::METHOD_PUT,
    self::METHOD_POST,
    self::METHOD_DELETE,
    ];
  
  /** @var array $jsonErrors All currently defined JSON errors and their description */
  protected array $jsonErrors = [
    JSON_ERROR_NONE                   => 'No error occurred',
    JSON_ERROR_DEPTH                  => 'The maximum stack depth has been reached',
    JSON_ERROR_CTRL_CHAR              => 'Control character issue, maybe wrong encoded',
    JSON_ERROR_SYNTAX                 => 'Syntaxerror',
    JSON_ERROR_UTF8 	                => 'Malformed UTF-8 characters, possibly incorrectly encoded',
    JSON_ERROR_RECURSION 	            => 'One or more recursive references in the value to be encoded',
    JSON_ERROR_INF_OR_NAN             => 'One or more NAN or INF values in the value to be encoded',
    JSON_ERROR_UNSUPPORTED_TYPE       => 'A value of a type that cannot be encoded was given',
    JSON_ERROR_INVALID_PROPERTY_NAME  => 'A property name that cannot be encoded was given',
    JSON_ERROR_UTF16                  => 'Malformed UTF-16 characters, possibly incorrectly encoded'
    ];
  
  /** @var string Last called API url (for error reporting) */
  protected string $lastApiUrl = "";
  
  /**
   * Initializes curl
   * @throws Exception
   */
  public function __construct()
    {
    if(function_exists('curl_init') === false)
      {
      throw new BadMethodCallException("cURL extension is missing!");
      }
    $this->curl     = curl_init();
    $curloptions    = array(
      CURLOPT_RETURNTRANSFER    => true,
      CURLOPT_FOLLOWLOCATION    => false,
      CURLOPT_USERAGENT         => self::USER_AGENT,
      CURLOPT_HTTPHEADER        => ['Content-Type: application/json; charset=utf-8'],
      CURLOPT_FORBID_REUSE      => true,
      CURLOPT_FRESH_CONNECT     => true,
      CURLOPT_BUFFERSIZE        => 1024*1024,
      );
    if(curl_setopt_array($this->curl,$curloptions) === false)
      {
      throw new Exception(curl_error($this->curl));
      }
    }
  
  /**
   * Initializes credentials for InoCore connections
   * @param string $user API username
   * @param string $pass API password
   * @param string $apiurl API Url (will be checked)
   * @return bool
   * @throws Exception
   */
  public function Init(string $user,string $pass, string $apiurl):bool
    {
    if(filter_var($apiurl, FILTER_VALIDATE_URL) === false)
      {
      throw new Exception("API Url is not valid!");
      }
    $this->setApiUrl( rtrim($apiurl, '/') . '/');
    $this->setApiUser($user);
    $this->setApiPass($pass);
    return true;
    }
  
  /**
   * Checks if token exists, else asks the core for a new one.
   * Returns the token.
   * @return bool
   * @throws Exception
   * @throws InvalidArgumentException
   */
  public function HasValidToken():bool
    {
    $tdate  = new DateTime(date("d.m.Y H:i:s",$this->getExpires()));
    $now    = new DateTime("now");
    $token  = $this->getToken();
    if( $token === "" || $now > $tdate)
      {
      if($this->getApiUser() === "")
        {
        throw new InvalidArgumentException('No credentials found - make sure to set them on initial login!!!!');
        }
      $token = $this->FetchToken();
      if($this->getToken() === "")
        {
        return false;
        }
      }
    return true;
    }
  
  /**
   * Retrieves access token and save it.
   * @throws Exception
   */
  public function FetchToken():string
    {
    error_log("FETCH TOKEN CALLED");
    $copts = array(
      CURLOPT_HTTPAUTH      => CURLAUTH_BASIC,
      CURLOPT_USERPWD       => $this->getApiUser() . ':' . $this->getApiPass(),
      CURLOPT_URL           => $this->getApiUrl().'token',
      CURLOPT_CUSTOMREQUEST => self::METHOD_POST,
      );
    curl_setopt_array($this->curl,$copts);
    $result = curl_exec($this->curl);
    if($result === FALSE)
      {
      throw new Exception(curl_error($this->curl),1);
      }
    $httpCode = (int)curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
    if($httpCode !== 201)
      {
      error_log("coreConnectBase/GetToken(): Unauthorized [$httpCode]!");
      throw new Exception("Unauthorized",$httpCode);
      }
    $tk = json_decode($result,true);
    if($tk === false)
      {
      error_log("coreConnectBase/GetToken(): JSON decode failed: ".$this->jsonErrors[json_last_error()]);
      throw new Exception('JSON error - cannot get token?');
      }
    if(isset($tk['token']) === false)
      {
      error_log("coreConnectBase/GetToken(): No token data found in payload?");
      throw new Exception('API error - cannot get token?');
      }
    // Newer versions of JWT returns a DateTimeImmutable object instead of a timestamp???? WTF?
    if(is_array($tk['expires']) === true && isset($tk['expires']['date']) === true)
      {
      $ds = new DateTimeImmutable($tk['expires']['date']);
      if(isset($tk['expires']['timezone']) === true)
        {
        $ds->setTimezone(new DateTimeZone($tk['expires']['timezone']));
        }
      $expire = $ds->getTimestamp();
      }
    else
      {
      $expire = (int)$tk['expires'];
      }
    $this->setToken($tk['token']);
    $this->setExpires($expire);
    $this->setJwtUser(json_encode($tk['user']));
    return $tk['token'];
    }

  /**
   * Generic HTTP call method.
   * @param $url
   * @param string $method
   * @param array $data
   * @param array $params
   * @return array
   * @throws Exception
   */
  public function call($url, string $method = self::METHOD_GET, array $data = [], array $params = []):array
    {
    $this->lastApiUrl = $url;
    if (!in_array($method, $this->validMethods))
      {
      throw new Exception('Invalid HTTP method: ' . $method);
      }
    // At this stage communication can be done only via auth token!
    if($this->HasValidToken() === false)
      {
      throw new Exception("No token available?!");
      }
    $queryString = '';
    if (!empty($params))
      {
      $queryString = http_build_query($params);
      $url = rtrim($url, '?') . '?';
      }
    $url = $this->getApiUrl() . $url . $queryString;
    $dataString = json_encode($data);
    $copt = array(
      CURLOPT_URL             => $url,
      CURLOPT_CUSTOMREQUEST   => $method,
      CURLOPT_POSTFIELDS      => $dataString,
      CURLOPT_HTTPHEADER      => [
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Bearer ' . $this->getToken(),
        ],
      );
    curl_setopt_array($this->curl,$copt);
    $result = curl_exec($this->curl);
    if($result === FALSE)
      {
      throw new Exception(curl_error($this->curl)." [APICALL: {$this->lastApiUrl}]",1);
      }
    $httpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
    return $this->prepareResponse($result, $httpCode);
    }
  
  /**
   * Perform a GET request.
   * @param string $url
   * @param array $params
   * @return array
   * @throws Exception
   */
  public function get(string $url, array $params = []):array
    {
    return $this->call($url, self::METHOD_GET, [], $params);
    }
  
  /**
   * Perform a POST request.
   * @param string $url
   * @param array $data
   * @param array $params
   * @return array
   * @throws Exception
   */
  public function post(string $url, array $data = [], array $params = []):array
    {
    return $this->call($url, self::METHOD_POST, $data, $params);
    }
  
  /**
   * Perform a PUT request.
   * @param string $url
   * @param array $data
   * @param array $params
   * @return array
   * @throws Exception
   */
  public function put(string $url, array $data = [], array $params = []):array
    {
    return $this->call($url, self::METHOD_PUT, $data, $params);
    }
  
  /**
   * Perform a DELETE request.
   * @param string $url
   * @param array $params
   * @return array
   * @throws Exception
   */
  public function delete(string $url, array $params = []): array
    {
    return $this->call($url, self::METHOD_DELETE, [], $params);
    }
  
  /**
   * Parses result from API
   * @param string $result Result from curl_exec()
   * @param int $httpCode HTTP Status code
   * @return array
   * @throws Exception
   * @throws BadMethodCallException
   */
  protected function prepareResponse(string $result, int $httpCode):array
    {
    //error_log("---=---=------=------=------=------=------=------=------=------=------=---");
    //error_log(var_export($result,true));
    //error_log("--------------------------------------------------------------------------");
    if (null === $decodedResult = json_decode($result, true))
      {
      throw new Exception('Could not decode json: '.$this->jsonErrors[json_last_error()].' [HTTP code: '.$httpCode.' | APICALL: '.$this->getLastUrl().']'."\n".var_export($result,true),$httpCode);
      }
    if (!isset($decodedResult['statusCode']))
      {
      throw new BadMethodCallException("invalid response! [APICALL: {$this->getLastUrl()}]",$httpCode);
      }
    if (!isset($decodedResult['data']))
      {
      $errmsg = "";
      if (array_key_exists('error', $decodedResult) && is_array($decodedResult['error']))
        {
        $errmsg = $decodedResult['error']['description']." [APICALL: {$this->getLastUrl()}]";
        }
      throw new Exception($errmsg,$httpCode);
      }
    if(isset($decodedResult['data']['error']) === true)
      {
      throw new Exception($decodedResult['data']['error']." [APICALL: {$this->getLastUrl()}]",$httpCode);
      }
    return $decodedResult;
    }
  
  /**
   * Returns the last API url call
   * @return string
   */
  public function getLastUrl():string
    {
    return $this->lastApiUrl;
    }
  
  }
