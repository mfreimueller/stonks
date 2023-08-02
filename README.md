# Stonks!

A simplistic stock watcher for personal use, developed in PHP and using the [Alphavantage API](https://alphavantage.co).

## shares.json

```
"7CD.FRK": {
		"name": "CD Projekt Red",
		"volume": 25,
		"rule": {
			"price": 100,
			"operation": "ge"
		}
	}
```

* "7CD.FRK" is the symbol from alphavantage
* "name" is the display name you give the stock on the website
* "volume" is how much shares you own
* "rule" is optional and defines when an alert email should be sent to you
* "price" is the target price that must be reached
* "operation" defines, via "ge" (greater or equal) or "le" (lesser or equal), whether "price" must be smaller or larger the current price of the stock