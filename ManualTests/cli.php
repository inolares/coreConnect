<?php declare(strict_types=1);
/**
 * Test for CLI class.
 * @package coreConnect
 * @author Sascha 'SieGeL' Pfalz <s.pfalz@inolares.de>
 * @version 1.0.0 (12-Jun-2022)
 */

use inolares\coreConnectCLI;

require_once '../src/coreConnectBase.php';
require_once '../examples/coreConnectCLI.php';

if(php_sapi_name() != 'cli')
  {
  die("Script is meant to be started on CLI only!");
  }

require_once 'credentials.inc.php';

try
  {
  $cc = new coreConnectCLI();
  // We clear first our credentials list to allow testing new creds
  $cc->clearCredentials();
  $cc->init(getenv('CC_USER'),getenv('CC_PASS'),getenv('CC_HOST'));
  $cc->setCurlOpts([CURLOPT_SSL_VERIFYPEER => false]);
  echo "\nTesting v1/ping:\n";
  print_r($cc->get("v1/ping"));
  echo "\nTesting v1/admin/versions:\n";
  print_r($cc->get('v1/admin/versions'));
  echo "\nTesting v1/admin/data:\n";
  print_r($cc->get('v1/admin/data/1754464844/1754464847?sort%5B0%5D%5Bproperty%5D=log_date&sort%5B0%5D%5Bdirection%5D=DESC&limit=1000&filter%5B0%5D%5Bproperty%5D=source_id&filter%5B0%5D%5Bexpression%5D=in&filter%5B0%5D%5Bvalue%5D=238669'));
  }
catch(Exception $e)
  {
  die("InoCore Error: ".$e->getMessage());
  }
