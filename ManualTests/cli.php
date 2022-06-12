<?php
/**
 * Test for CLI class.
 * @package coreConnect
 * @author Sascha 'SieGeL' Pfalz <s.pfalz@inolares.de>
 * @version 1.0.0 (12-Jun-2022)
 */

use inolares\coreConnectCLI;

require_once '../coreConnectBase.php';
require_once '../coreConnectCLI.php';

if(php_sapi_name() != 'cli')
  {
  die("Script is meant to be started on CLI only!");
  }

try
  {
  $cc = new coreConnectCLI();
  // We clear first our credentials list to allow testing new creds
  $cc->clearCredentials();
  $cc->init('purebasic@localhost','purebasic','http://inocore.fritz.box');
  echo "\nTesting v1/ping:\n";
  print_r($cc->get("v1/ping"));
  echo "\nTesting v1/admin/versions:\n";
  print_r($cc->get('v1/admin/versions'));
  }
catch(Exception $e)
  {
  die("InoCore Error: ".$e->getMessage());
  }
