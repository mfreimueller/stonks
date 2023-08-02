<?php

$config = require("config.php");

function shouldDebugEmail() {
	global $config;
	return $config["debug_email"];
}

function getBaseDirectory() {
	global $config;
	return $config["base_directory"];
}

function getCachePath() {
	return getBaseDirectory() . "/cache";
}


function getAPIKey() {
	global $config;
	return $config["api_key"];
}

function getSharesJson() {
	if (file_exists(getBaseDirectory() . "/shares.json")) {
		return json_decode(file_get_contents(getBaseDirectory() . "/shares.json"), true);
	} else {
		return array();
	}
}

function getDailyShareData($symbol, $raw = false, $cache = true) {
	if ($cache) {
		$cachePath = getCachePath() . "/" . $symbol . ".json";

		if (file_exists($cachePath)) {
			$json = file_get_contents($cachePath);
		} else {
			$json = getDailyShareData($symbol, true, false);
			file_put_contents($cachePath, $json);
		}
	} else {
		$json = file_get_contents('https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=' . $symbol . '&apikey=' . getAPIKey());
	}

	return $raw ? $json : json_decode($json, true);
}

function writeCache($symbol, $json) {
	file_put_contents(getCachePath() . "/" . $symbol . ".json", $json);
}

function now()
{
	return date("d.m.y");
}

function sendEmail($stocksToReport)
{
	$date = now();

	$to = 'michael@mfreimueller.com';
	$subject = '!!! Kurswarnung vom ' . $date . ' !!! | Stonks!™';
	$message = "Achtung, Achtung!\n\
				Der Stonks!™-Alert hat angeschlagen, für folgende Kurse:\n";
	$headers = 'From: mail@mfreimueller.com' . "\r\n" .
		'Reply-To: mail@mfreimueller.com' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();

	foreach ($stocksToReport as $stockToReport) {
		$message = $message . "\n" . $stockToReport;
	}

	$message = $message . "\n\nGood Day, Sir!\nStonk(s!™) away!";

	_mail($to, $subject, $message, $headers);
}

function _mail($to, $subject, $message, $headers) {
	if (shouldDebugEmail()) {
		echo "MAIL TO " . $to . "<br><br>" . 
			$subject . "<br><br>" .
			str_replace("\n", "<br>", $message);
	} else {
		mail($to, $subject, $message, $headers);
	}
}

// ?>