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
		writeCache($share, $json);
	}
}

// ?>