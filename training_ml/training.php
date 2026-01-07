<?php
$record_file = fopen("/var/www/html/record_index.txt", "a");
$txt = "training\n";
$date = date('m/d/Y h:i:s a', time());
$txt2 = "trainingIP: " . $_SERVER["REMOTE_ADDR"] . " date: " . $date . "\n";
fwrite($record_file, $txt);
fwrite($record_file, $txt2);
fclose($record_file);
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>AlphaBit Training</title>
	<link rel="stylesheet" href="/assets/css/tstyle.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link rel="shortcut icon" type="image/x-icon" href="/assets/images/alphabit.ico" />
</head>

<body>
	<!-- Language Popup -->
	<div id="language-popup" class="language-popup-overlay" style="display: none;">
		<div class="language-popup-content">
			<h2>Choose Language / Alege Limba</h2>
			<div class="language-options">
				<button onclick="selectLanguage('ro')">🇷🇴 Română</button>
				<button onclick="selectLanguage('en')">🇬🇧 English</button>
			</div>
		</div>
	</div>

	<style>
		.language-popup-overlay {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(0, 0, 0, 0.9);
			z-index: 9999;
			display: flex;
			justify-content: center;
			align-items: center;
		}

		.language-popup-content {
			background-color: #1e1e1e;
			padding: 40px;
			border-radius: 15px;
			text-align: center;
			border: 1px solid #333;
			box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
		}

		.language-popup-content h2 {
			color: #fff;
			margin-bottom: 35px;
			font-family: Arial, sans-serif;
		}

		.language-options {
			display: flex;
			gap: 20px;
			justify-content: center;
		}

		.language-options button {
			padding: 15px 30px;
			font-size: 18px;
			cursor: pointer;
			background-color: #d4d4d4ff;
			color: black;
			border: none;
			border-radius: 8px;
		}

		.language-options button:hover {
			background-color: #ffffffff;
			transform: scale(1.05);
		}
	</style>

	<script>
		function setCookie(name, value, days) {
			var expires = "";
			if (days) {
				var date = new Date();
				date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
				expires = "; expires=" + date.toUTCString();
			}
			document.cookie = name + "=" + (value || "") + expires + "; path=/";
		}

		function getCookie(name) {
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for (var i = 0; i < ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0) == ' ') c = c.substring(1, c.length);
				if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
			}
			return null;
		}

		function selectLanguage(lang) {
			setCookie('site_lang', lang, 365);
			document.getElementById('language-popup').style.display = 'none';
			location.reload();
		}

		document.addEventListener("DOMContentLoaded", function () {
			var lang = getCookie('site_lang');
			if (!lang) {
				document.getElementById('language-popup').style.display = 'flex';
			}
		});
	</script>

	<noscript>You need to enable JavaScript to run this website.</noscript>
	<?php
	$lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en';
	?>
	<div class="background-container">
		<div class="ai-star-logo">
			<img src="assets/images/ai_star_alpha.png" width=55>
		</div>
		<div class="alphabit-topleft">
			<a href="/">AlphaBit OpenML</a>
		</div>
		<div class="before_docs"><?php echo $season_year; ?></div>
		<?php if ($lang == 'ro'): ?>
			<div class="welcome-text">
				Fiecare fotografie pe care o distribuiți îmbunătățește viziunea robotului nostru ✨.
				<p>Haideți să dominăm terenul!</p>
			</div>

			<div class="background-button">
				<a href="upload">Încarcă imagine</a>
			</div>
			<div class="learn-text">
				Încărcați imagini cu orice obiecte, camere sau locații.
				<p>Simțiți-vă liber să capturați orice doriți</p>
			</div>
		<?php else: ?>
			<div class="welcome-text">
				Every photo you share sharpens our robot’s vision ✨.
				<p>Let’s dominate the field!</p>
			</div>

			<div class="background-button">
				<a href="upload">Upload image</a>
			</div>
			<div class="learn-text">
				Upload images of any objects, room or location.
				<p>Feel free to capture anything you want</p>
			</div>
		<?php endif; ?>
	</div>
</body>

</html>