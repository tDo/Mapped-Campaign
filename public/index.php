<?php
// Require configuration class
ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);

//die(__DIR__ .'/../Libraries/Configuration.php');
require_once __DIR__ .'/../Libraries/Configuration.php';

$config = new \Libraries\Configuration(__DIR__ .'/../Configs/default.json');

// And after the configuration was loaded the actual application
require_once(__DIR__ .'/app.php');
