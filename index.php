<?php

require_once './vendor/autoload.php';
$timeTicker = 3000;
$config = require_once './config/config.php';
$server = new DelayQueue\Http($config);
$server->init();