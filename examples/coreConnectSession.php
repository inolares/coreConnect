<?php declare(strict_types=1);
/**
 * Class uses PHP's session storage to store InoCore credentials.
 * Use this class for plain PHP scripts that have an active session.
 * @package coreConnect
 * @author Sascha 'SieGeL' Pfalz <s.pfalz@inolares.de>
 * @copyright Inolares GmbH & Co. KG
 * @version 2.0.0 (11-Jun-2022)
 * @license BSD
 */

namespace inolares;

use Exception;

class coreConnectSession extends coreConnectBase
  {
  /* Session keys */
  const KEY_USER      = "coreConnectSession/U";
  const KEY_PASS      = "coreConnectSession/P";
  const KEY_APIURL    = "coreConnectSession/A";
  const KEY_TOKEN     = "coreConnectSession/T";
  const KEY_EXPIRES   = "coreConnectSession/E";
  const KEY_JWTTOKEN  = "coreConnectSession/J";

  /**
   * @throws Exception
   */
  public function __construct()
    {
    parent::__construct();
    if(function_exists('session_start') === false)
      {
      throw new Exception('No session extension found!');
      }
    if(session_status() !== PHP_SESSION_ACTIVE)
      {
      throw new Exception("No active session found!");
      }
    }
  
  /** @inheritDoc */
  protected function setApiUrl(string $url):void
    {
    $_SESSION[self::KEY_APIURL] = $url;
    }
  
  /** @inheritDoc */
  protected function getApiUrl():string
    {
    return $_SESSION[self::KEY_APIURL] ?? "";
    }
  
  /** @inheritDoc */
  protected function setApiUser(string $user):void
    {
    $_SESSION[self::KEY_USER] = $user;
    }
  
  /** @inheritDoc */
  protected function getApiUser():string
    {
    return $_SESSION[self::KEY_USER] ?? "";
    }
  
  /** @inheritDoc */
  protected function setApiPass(string $pass):void
    {
    $_SESSION[self::KEY_PASS] = $pass;
    }
  
  /** @inheritDoc */
  protected function getApiPass():string
    {
    return $_SESSION[self::KEY_PASS] ?? "";
    }
  
  /** @inheritDoc */
  protected function setExpires(int $timestamp):void
    {
    $_SESSION[self::KEY_EXPIRES] = $timestamp;
    }
  
  /** @inheritDoc */
  protected function getExpires():int
    {
    return $_SESSION[self::KEY_EXPIRES] ?? 0;
    }
  
  /** @inheritDoc */
  protected function setToken(string $token):void
    {
    $_SESSION[self::KEY_TOKEN] = $token;
    }
  
  /** @inheritDoc */
  protected function getToken():string
    {
    return $_SESSION[self::KEY_TOKEN] ?? "";
    }
  
  /** @inheritDoc */
  protected function setJwtUser(string $json_data):void
    {
    $_SESSION[self::KEY_JWTTOKEN] = $json_data;
    }
  
  /** @inheritDoc */
  protected function getJwtUser():string
    {
    return $_SESSION[self::KEY_JWTTOKEN] ?? "";
    }
  }
