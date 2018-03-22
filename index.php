<?php
session_start();
date_default_timezone_set('PRC');
include 'boot/Start.php';
// ini_set("display_errors","on");
// error_reporting(E_ALL);
Start::init();
Start::route();