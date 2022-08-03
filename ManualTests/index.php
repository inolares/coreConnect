<?php declare(strict_types=1);

use inolares\coreConnectSession;

echo "<pre>";

require_once '../src/coreConnectBase.php';
require_once '../examples/coreConnectSession.php';
require_once 'credentials.inc.php';

session_name("ccManTestSession");
session_start();

try
  {
  $cc = new coreConnectSession();
  // We clear first our credentials list to allow testing new creds
  $cc->clearCredentials();
  $cc->init(getenv('CC_USER'),getenv('CC_PASS'),getenv('CC_HOST'));
  echo "\nTesting v1/ping:\n";
  print_r($cc->get("v1/ping"));
  echo "\nTesting v1/admin/versions:\n";
  print_r($cc->get('v1/admin/versions'));
  }
catch(Exception $e)
  {
  die("InoCore Error: ".$e->getMessage());
  }
