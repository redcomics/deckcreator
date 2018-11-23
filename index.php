<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Ninuno Deck Creator</title>
	<link rel="stylesheet" href="assets/css/deckcreator.css">
</head>
<body id="body">
	<div id="wrapper">
		<div id="wrapper_row">
			<div id="preview_block">
				<img src="../assets/img/NinunoCardBack.png" alt="Ninuno Card Game" id="preview_image">
			</div>
			<div id="left_content">
				<form>
					<input type="text" id="card_search" placeholder="Search card name">
				</form>
			</div>
			<div id="right_content"></div>
		</div>
		<div id="legend_row">
			<h3>
				<span>Cards in Deck:</span>
				<span id="deck_card_count" class="insufficient">0</span>
				<span>/ 40</span>
			</h3>
		</div>
	</div>
	<script type="text/javascript" src="assets/js/deckcreator.js"></script>
</body>
</html>