<?php declare(strict_types=1);
/**
 * Test for continous processing of InoCore data.
 * @package coreConnect
 * @author Sascha 'SieGeL' Pfalz <s.pfalz@inolares.de>
 * @version 2.0.1 (02-Aug-2022)
 */

use inolares\coreConnectCLI;

require_once '../src/coreConnectBase.php';
require_once '../examples/coreConnectCLI.php';

if(php_sapi_name() != 'cli')
{
  die("Script is meant to be started on CLI only!");
}

require_once 'credentials.inc.php';

$cc = new coreConnectCLI();
// We clear first our credentials list to allow testing new creds
$cc->clearCredentials();
$cc->init(getenv('CC_USER'),getenv('CC_PASS'),getenv('CC_HOST'));
$tstart = time();
while(1)
  {
  $pong = $cc->get("v1/ping");
  printf("PING %15s.....: %15s (Seconds since start: %5.2fs)\n",microtime(true),$pong['data']['PONG'],microtime(true) - $tstart);
  sleep(1);
  }
