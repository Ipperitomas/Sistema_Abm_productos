<?php
define("DEVELOPE",true);
define("DEPLOY","DEV");

if (DEVELOPE) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}
define("PEPPER_FILE","pepper.txt");

?>