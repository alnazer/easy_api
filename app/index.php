<?php
require_once "../vendor/autoload.php";
require_once "Config/config.php";
use Alnazer\Easyapi\System\Application;
$app = new  Application();
$app->run($config);

