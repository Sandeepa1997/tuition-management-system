<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define("WEB_URL", $_ENV['WEB_URL']);
define("SYS_URL", $_ENV['SYS_URL']);
?>
