<?php  
require "../../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookChecker;

$cookie = 'yourcookie';

$check = new FacebookChecker();
$results =$check->CheckLiveCookie($cookie);

// (bool)
echo $results;