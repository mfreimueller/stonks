<?php
include_once 'util.php';

$shareIds = getSharesJson();
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Stonks!™</title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
	<link href="css/main.css" rel="stylesheet">

	<link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
	<link rel="manifest" href="favicon/site.webmanifest">
	<link rel="mask-icon" href="favicon/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">
</head>

<body>
	<div class="container">
		<div class="m-3 row" style="text-align: center;">
			<h1 class="display-3">Stonks!</h1>
		</div>

		<div class="row">
			<div class="col-3">
				<ul class="nav flex-column">
					<?php
					foreach (array_keys($shareIds) as $shareId) { ?>
						<li class="nav-item">
							<a class="nav-link" aria-current="page" data-stock="<?= $shareId ?>" href="#">
							<?php if ($shareIds[$shareId]["volume"] > 0) { ?>
								<i class="bi bi-star-fill"></i>&nbsp;
							<?php } ?>
								<?= $shareIds[$shareId]["name"] ?>
							</a>
						</li>
					<?php } ?>
				</ul>
			</div>
			<div class="main col" style="display: none;">
				<h1></h1>
				<h2>
					<span class="badge text-bg-primary" title="Open"></span>
					<span class="badge text-bg-success" title="High"></span>
					<span class="badge text-bg-warning" title="Low"></span>
					<span class="badge text-bg-secondary" title="Close"></span>
				</h2>

				<canvas style="width: 100%;" id="stockChart"></canvas>

				<div>
					<button type="button" class="m-3 btn btn-info btn-modal" data-bs-toggle="modal"
						data-bs-target="#emailRulesModal">
						E-Mail Rules
					</button>

					<button type="button" class="volume m-3 btn btn-secondary"></button>
				</div>

				<h2>Recent Data</h2>
				<table class="table">
					<thead>
						<tr>
							<th scope="col">Date</th>
							<th scope="col">Open</th>
							<th scope="col">High</th>
							<th scope="col">Low</th>
							<th scope="col">Close</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="modal fade" id="emailRulesModal" tabindex="-1" aria-labelledby="emailRulesModalLabel"
		aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="emailRulesModalLabel"></h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p><strong>Send E-Mail if ...</strong></p>

					<ul class="list-unstyled">
					</ul>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive"
		aria-atomic="true">
		<div class="d-flex">
			<div class="toast-body">
				API failed to respond properly. Check the error logs.
			</div>
			<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
				aria-label="Close"></button>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.7.0.min.js"
		integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
		crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.2/dist/chart.umd.min.js"></script>
	<script src="js/main.js"></script>

	<script>
		var stockMetaData = <?= json_encode($shareIds); ?>;

		$(document).ready(function () {
			initializeStocks(stockMetaData);
		});
	</script>
</body>

</html>