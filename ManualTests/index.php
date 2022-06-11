<?php declare(strict_types=1);

use inolares\coreConnectSession;

echo "<pre>";

require_once '../coreConnectBase.php';
require_once '../coreConnectSession.php';

session_name("ccManTestSession");
session_start();

$cc = new coreConnectSession();
$cc->Init('admin@localhost','admin2k19','http://inocore.fritz.box');

echo "\nTesting v1/ping:\n";

print_r($cc->get("v1/ping"));

echo "\nTesting v1/admin/versions:\n";
print_r($cc->get('v1/admin/versions'));
