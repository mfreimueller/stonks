<?php

include_once 'util.php';

function createCache() {
	$shares = getSharesJson();
	
	$minuteCounter = 0;
	foreach (array_keys($shares) as $share) {
		if (++$minuteCounter >= 4) {
			sleep(60);
			$minuteCounter = 0;
		}
		
		$json = getDailyShareData($share, true, false);

		// write the retrieved json data iff we received valid data.
		if (json !== false) {
			writeCache($share, $json);
		}
	}
}

function writeCache($symbol, $json) {
	file_put_contents(getCachePath() . "/" . $symbol . ".json", $json);
}

// ?>