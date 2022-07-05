<?php declare(strict_types=1);
/**
 * Use this class as an example how to implement for non-session scripts, i.e. CLI.
 * @package coreConnect
 * @author Sascha 'SieGeL' Pfalz <s.pfalz@inolares.de>
 * @copyright Inolares GmbH & Co. KG
 * @version 2.0.0 (11-Jun-2022)
 * @license BSD
 */

namespace inolares;

class coreConnectCLI extends coreConnectBase
  {
  /** @var string API username */
  private string $user = "";
  /** @var string API password */
  private string $pass = "";
  /** @var string API Base URL */
  private string $apiurl = "";
  /** @var int Expires timestamp */
  private int $expires = -1;
  /** @var string Access token */
  private string $token = "";
  /** @var string JWT Userdata as JSON */
  private string $jwt_user = "";
  
  /**
   * @inheritDoc
   */
  protected function setApiUrl(string $url): void
    {
    $this->apiurl = $url;
    }
  
  /**
   * @inheritDoc
   */
  protected function getApiUrl(): string
    {
    return $this->apiurl;
    }
  
  /**
   * @inheritDoc
   */
  protected function setApiUser(string $user): void
    {
    $this->user = $user;
    }
  
  /**
   * @inheritDoc
   */
  protected function getApiUser(): string
    {
    return $this->user;
    }
  
  /**
   * @inheritDoc
   */
  protected function setApiPass(string $pass): void
    {
    $this->pass = $pass;
    }
  
  /**
   * @inheritDoc
   */
  protected function getApiPass(): string
    {
    return $this->pass;
    }
  
  /**
   * @inheritDoc
   */
  protected function setExpires(int $timestamp): void
    {
    $this->expires = $timestamp;
    }
  
  /**
   * @inheritDoc
   */
  protected function getExpires(): int
    {
    return $this->expires;
    }
  
  /**
   * @inheritDoc
   */
  protected function setToken(string $token): void
    {
    $this->token = $token;
    }
  
  /**
   * @inheritDoc
   */
  protected function getToken(): string
    {
    return $this->token;
    }
  
  /**
   * @inheritDoc
   */
  protected function setJwtUser(string $json_data): void
    {
    $this->jwt_user = $json_data;
    }
  
  /**
   * @inheritDoc
   */
  protected function getJwtUser(): string
    {
    return $this->jwt_user;
    }
  }
