<?php

include_once 'util.php';
include_once 'cache.php';

$forceReport = key_exists("force_report", $_GET) ? (strcmp($_GET["force_report"], "1") == 0) : false;
$skipCacheRebuild = key_exists("skip_cache", $_GET) ? (strcmp($_GET["skip_cache"], "1") == 0) : false;

$shares = getSharesJson();
$stocksToReport = array();

// forcing a cache refresh.
// this takes a while, as the API is throttled.
if (!$skipCacheRebuild) {
	createCache();
}

foreach (array_keys($shares) as $shareId) {
	$share = $shares[$shareId];

	if (key_exists("rule", $share)) {
		// first, extract the latest "high" price
		$dailyShareData = getDailyShareData($shareId);

		if ($dailyShareData === false) {
			error_log("Failed to retrieve daily share data for key " . $shareId);
			continue;
		}

		$lastRefreshed = $dailyShareData["Meta Data"]["3. Last Refreshed"];
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