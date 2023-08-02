<?php

include_once 'util.php';
include_once 'cache.php';

$forceReport = key_exists("forceReport", $_GET) ? (strcmp($_GET["forceReport"], "1") == 0) : false;

$shares = getSharesJson();
$stocksToReport = array();

// forcing a cache refresh.
// this takes a while, as the API is throttled.
createCache();

foreach (array_keys($shares) as $shareId) {
	$share = $shares[$shareId];

	if (key_exists("rule", $share)) {
		// first, extract the latest "high" price
		$dailyShareData = getDailyShareData($shareId);

		$lastRefreshed = $dailyShareData["3. Last Refreshed"];
		$timeSeries = $dailyShareData["Time Series (Daily)"];

		$lastTimeSeries = $timeSeries[$lastRefreshed];
		$latestHigh = floatval($lastTimeSeries["2. high"]);
		$latestLow = floatval($lastTimeSeries["3. low"]);

		// then check with our rule
		$rule = $share["rule"];
		$price = $rule["price"];
		$operation = $rule["operation"];

		switch ($operation) {
			case "ge":
				if ($latestHigh >= $price || $forceReport) {
					$stocksToReport[] = $share["name"] . ": " . $latestHigh . "  >= " . $price;
				}
				break;
			case "le":
				if ($latestLow <= $price || $forceReport) {
					$stocksToReport[] = $share["name"] . ": " . $latestLow . "  <= " . $price;
				}
				break;
			default:
				error_log("Invalid rule operation for " . $shareId . ": " . $operation);
				break;
		}
	}
}

if (count($stocksToReport) > 0) {
	sendEmail($stocksToReport);
}

// ?>