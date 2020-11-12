<?php
namespace Ruthvens;

// error_reporting(0);
set_time_limit(0);
ini_set("memory_limit","-1");
date_default_timezone_set('Asia/Jakarta');

defined('PATH') or define('PATH', __DIR__);
require PATH . '/ruthvens.lib/loader.php';

$ruthvens = new \Prepare($config);
