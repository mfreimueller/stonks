function initializeStocks(stockMetaData) {
	var stockChart = null;

	$(".nav-link").click(function () {
		var stockId = $(this).data("stock");

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