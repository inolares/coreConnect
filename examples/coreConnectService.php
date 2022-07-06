<?php declare(strict_types=1);
/**
 * Example implementation for a symfony service utilizing coreConnectBase class.
 * This class handles also both webusage via sessions and CLI usage by using class-internal variables.
 * @author Sascha 'SieGeL' Pfalz <s.pfalz@inolares.de>
 * @version 2.0.0
 * @license BSD
 */

namespace App\Service;

use Exception;
use inolares\coreConnectBase;
use Symfony\Component\HttpFoundation\RequestStack;

class coreConnectService extends coreConnectBase
  {
  /*
   * Storage for CLI commands
   */
  private string  $cc_api_url   = "";
  private string  $cc_api_user  = "";
  private string  $cc_api_pass  = "";
  private int     $cc_expires   = 0;
  private string  $cc_token     = "";
  private string  $cc_jwt_user  = "";

  /** @var bool True if running as command, else false */
  private bool    $isCli;

  /** @var RequestStack $requestStack */
  private RequestStack $requestStack;
  
  /**
   * @throws Exception
   */
  public function __construct(RequestStack $requestStack)
    {
    parent::__construct();
    if(php_sapi_name() === 'cli')
      {
      $this->isCli = true;
      }
    else
      {
      $this->isCli = false;
      }
    $this->requestStack = $requestStack;
    }
  
  /**
   * @inheritDoc
   */
  protected function setApiUrl(string $url): void
    {
    $this->setString('cc_api_url',$url);
    }
  
  /**
   * @inheritDoc
   */
  protected function getApiUrl(): string
    {
    return $this->getString('cc_api_url');
    }
  
  /**
   * @inheritDoc
   */
  protected function setApiUser(string $user): void
    {
    $this->setString('cc_api_user',$user);
    }
  
  /**
   * @inheritDoc
   */
  protected function getApiUser(): string
    {
    return $this->getString('cc_api_user');
    }
  
  /**
   * @inheritDoc
   */
  protected function setApiPass(string $pass): void
    {
    $this->setString('cc_api_pass',$pass);
    }
  
  /**
   * @inheritDoc
   */
  protected function getApiPass(): string
    {
    return $this->getString('cc_api_pass');
    }
  
  /**
   * @inheritDoc
   */
  protected function setExpires(int $timestamp): void
    {
    if($this->isCli)
      {
      $this->cc_expires = $timestamp;
      }
    else
      {
      $this->requestStack->getSession()->set('cc_expires',$timestamp);
      }
    }
  
  /**
   * @inheritDoc
   */
  protected function getExpires(): int
    {
    if($this->isCli)
      {
      return $this->cc_expires;
      }
    else
      {
      return (int)$this->requestStack->getSession()->get('cc_expires',0);
      }
    }
  
  /**
   * @inheritDoc
   */
  protected function setToken(string $token): void
    {
    $this->setString('cc_token',$token);
    }
  
  /**
   * @inheritDoc
   */
  protected function getToken(): string
    {
    return $this->getString('cc_token');
    }
  
  /**
   * @inheritDoc
   */
  protected function setJwtUser(string $json_data): void
    {
    $this->setString('cc_jwt_user',$json_data);
    }
  
  /**
   * @inheritDoc
   */
  protected function getJwtUser(): string
    {
    return $this->getString('cc_jwt_user');
    }
  
  
  /**
   * Helper function to get a string.
   * @param string $key
   * @return string|null
   */
  private function getString(string $key):?string
    {
    if($this->isCli)
      {
      return $this->{$key};
      }
    else
      {
      return $this->requestStack->getSession()->get($key,"");
      }
    }
  
  /**
   * Helper function to set a string
   * @param string $key
   * @param string $value
   * @return void
   */
  private function setString(string $key, string $value):void
    {
    if($this->isCli)
      {
      $this->{$key} = $value;
      }
    else
      {
      $this->requestStack->getSession()->set($key,$value);
      }
    }
  }
