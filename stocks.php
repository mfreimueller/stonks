<?php

include_once 'util.php';

header("Content-Type: application/json");

if (key_exists("stock-id", $_GET)) {
	$symbol = $_GET['stock-id'];

	echo getDailyShareData($symbol, true);
} else {
	echo '{}';
}

// ?>