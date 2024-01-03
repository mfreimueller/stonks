function initializeStocks(stockMetaData) {
	var stockChart = null;

	populateDashboard(stockMetaData);

	$(".nav-link").click(function () {
		var stockId = $(this).data("stock");

		if (stockId == "dashboard") {
			$(".main.col").hide();
			$(".dashboard.col").show();
			return;
		} else {
			$(".main.col").show();
			$(".dashboard.col").hide();
		}

		$.get("stocks.php?stock-id=" + stockId, (stockData) => {
			
			$(".main.col").show();

			const stockMeta = stockMetaData[stockId];
			const data = stockData["Time Series (Daily)"];

			if (!data || Object.keys(stockData).length === 0) {
				$(".main.col").hide();
				new bootstrap.Toast($(".toast.text-bg-danger")).show();

				return;
			}

			if (stockMeta.volume > 0) {
				$(".volume.btn").show();
				$(".volume.btn").text(stockMeta.volume + " Stk.");
			} else {
				$(".volume.btn").hide();
			}

			$(".main.col h1").text(stockMeta.name);
			$("#emailRulesModalLabel").text(stockMeta.name);

			if (!!stockMeta.rule) {
				$(".modal .list-unstyled li").remove();

				const rule = stockMeta.rule;
				var ruleString = "price ";

				switch (rule.operation) {
					case "ge":
						ruleString = ruleString + " >= ";
						break;
					case "le":
						ruleString = ruleString + " <= ";
						break;
					default:
						break;
				}

				ruleString = ruleString + rule.price;

				$(".modal .list-unstyled").append(
					`<li>${ruleString}</li>`
				);

				$(".main.col .btn-modal").show();
			} else {
				$(".main.col .btn-modal").hide();
			}

			var labels = [];
			var open = [];
			var high = [];
			var low = [];
			var close = [];

			$(".main.col tbody tr").remove();

			const dates = Object.keys(data);

			// extract latest value to display highlighted
			const latestDate = data[dates[0]];
			$(".main.col .text-bg-primary").text(latestDate["1. open"]);
			$(".main.col .text-bg-success").text(latestDate["2. high"]);
			$(".main.col .text-bg-warning").text(latestDate["3. low"]);
			$(".main.col .text-bg-secondary").text(latestDate["4. close"]);

			Object.keys(data).forEach(date => {
				const entry = data[date];

				labels.push(dateFormat(date));
				open.push(entry["1. open"]);
				high.push(entry["2. high"]);
				low.push(entry["3. low"]);
				close.push(entry["4. close"]);

				var row =
					"<tr> \
					<td>" + dateFormat(date) + "</td> \
					<td>" + entry["1. open"] + "</td> \
					<td>" + entry["2. high"] + "</td> \
					<td>" + entry["3. low"] + "</td> \
					<td>" + entry["4. close"] + "</td> \
				</tr>";
				$(".main.col tbody").append(row);
			});

			if (stockChart !== null) {
				stockChart.destroy();

				$("#stockChart").removeAttr("width");
				$("#stockChart").removeAttr("height");
				$("#stockChart").removeAttr("style");
				$("#stockChart").attr("style", "width: 100%");
			}

			const ctx = document.getElementById('stockChart').getContext('2d');
			stockChart = new Chart(ctx, {
				type: 'line',
				data: {
					labels: labels.reverse(),
					datasets: [{
						label: 'Open',
						data: open.reverse(),
						borderColor: 'rgba(13, 110, 253, 1)',
						backgroundColor: 'rgba(13, 110, 253, 0.2)',
						borderWidth: 2,
						fill: true,
					}, {
						label: 'High',
						data: high.reverse(),
						borderColor: 'rgba(25, 135, 84, 1)',
						backgroundColor: 'rgba(25, 135, 84, 0.2)',
						borderWidth: 2,
						fill: true,
					}, {
						label: 'Low',
						data: low.reverse(),
						borderColor: 'rgba(255, 193, 7, 1)',
						backgroundColor: 'rgba(255, 193, 7, 0.2)',
						borderWidth: 2,
						fill: true,
					}, {
						label: 'Close',
						data: close.reverse(),
						borderColor: 'rgba(108, 117, 125, 1)',
						backgroundColor: 'rgba(108, 117, 125, 0.2)',
						borderWidth: 2,
						fill: true,
					}],
				},
				options: {
					responsive: false,
					maintainAspectRatio: false,
					scales: {
						x: {
							display: true,
							title: {
								display: true,
								text: 'Date',
							},
						},
						y: {
							display: true,
							title: {
								display: true,
								text: 'Stock Price',
							},
						},
					},
				},
			});
		});
	});
}

function populateDashboard(stockMetaData) {
	// get all stocks where we currently have a volume > 0
	let heldStocks = [];
	Object.keys(stockMetaData).forEach(key => {
		if (stockMetaData[key]["volume"] > 0) {
			heldStocks.push(key);
		}
	});

	function fetchData(remainingStocks, callback) {
		const stockId = remainingStocks.shift();

		function _callback(array) {
			$.get("stocks.php?stock-id=" + stockId, (stockData) => {
				const name = stockMetaData[stockId]["name"];
				const data = stockData["Time Series (Daily)"];
				const dates = Object.keys(data);

				// extract latest value to display highlighted
				const latestDate = data[dates[0]];
				const highToday = latestDate["2. high"];

				const dayBefore = data[dates[1]];
				const highBefore = dayBefore["2. high"];

				const diff = (highToday - highBefore).toFixed(3);

				let html = "<div class='tile'>";
				html += "<strong>" + name + "</strong><br>";

				if (diff > 0) {
					html += "<span class='green'>" + diff + "</span>";
				} else if (diff < 0) {
					html += "<span class='red'>" + diff + "</span>";
				} else {
					html += diff;
				}

				html += "<br><span class='small'>" + highToday + " - " + highBefore + "</span>";

				html += "</div>";

				array.push(html);
				callback(array);
			});
		};

		if (remainingStocks.length > 0) {
			fetchData(remainingStocks, _callback);
		} else {
			_callback([]);
		}
	}

	fetchData(heldStocks, stockData => {
		let html = "<div class='row row-cols-auto'>";

		for (let idx = 0; idx < stockData.length; idx++) {
			html += "<div class='col'>" + stockData[idx] + "</div>";
		}

		html += "</div>";

		$(".dashboard.container").append(html);
	});
}

function dateFormat(originalDateStr) {
	const originalDate = new Date(originalDateStr);

	// Extract day, month, and year components
	const day = originalDate.getDate();
	const month = originalDate.getMonth() + 1; // Months are zero-based, so we add 1
	const year = originalDate.getFullYear();

	// Format the date as "dd. mm. yyyy"
	const formattedDate = `${day}. ${month < 10 ? '0' : ''}${month}. ${year}`;

	return formattedDate;
}
